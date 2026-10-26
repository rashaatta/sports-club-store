<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryAdjustmentRequest;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->withStockTotals()
            ->with('category:id,name')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%');
            }))
            ->orderBy('name')
            ->get();

        $counts = [
            'total' => $products->count(),
            'low' => $products->filter(fn (Product $product) => $product->is_low_stock)->count(),
            'out' => $products->filter(fn (Product $product) => ! $product->is_in_stock)->count(),
            'variants' => (int) $products->sum('active_variants_count'),
        ];

        $filtered = $products
            ->when($status === 'low', fn ($collection) => $collection->filter(fn (Product $product) => $product->is_low_stock))
            ->when($status === 'out', fn ($collection) => $collection->filter(fn (Product $product) => ! $product->is_in_stock))
            ->when($status === 'in', fn ($collection) => $collection->filter(fn (Product $product) => $product->is_in_stock))
            ->values();

        $page = max(1, (int) $request->query('page', 1));
        $perPage = config('store.pagination.admin', 15);

        $paginator = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $recentAdjustments = InventoryAdjustment::query()
            ->with(['product:id,name,slug', 'variant:id,size,color', 'user:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.inventory.index', [
            'products' => $paginator,
            'status' => $status,
            'search' => $search,
            'counts' => $counts,
            'recentAdjustments' => $recentAdjustments,
            'reasons' => InventoryService::REASONS,
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category:id,name', 'variants']);

        $adjustments = InventoryAdjustment::query()
            ->where('product_id', $product->id)
            ->with(['variant:id,size,color', 'user:id,name'])
            ->latest()
            ->paginate(20);

        return view('admin.inventory.show', [
            'product' => $product,
            'adjustments' => $adjustments,
            'reasons' => InventoryService::REASONS,
        ]);
    }

    public function store(
        InventoryAdjustmentRequest $request,
        Product $product,
        InventoryService $inventory,
    ): RedirectResponse {
        $data = $request->validated();

        $variant = null;

        if (filled($data['variant_id'] ?? null)) {
            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->findOrFail($data['variant_id']);
        }

        $adjustment = $inventory->adjust(
            $product,
            $variant,
            $data['mode'],
            (int) $data['quantity'],
            $data['reason'],
            $data['note'] ?? null,
            $request->user(),
        );

        return redirect()
            ->route('admin.inventory.show', $product)
            ->with('status', $adjustment === null
                ? __('admin.inventory.no_change')
                : __('admin.inventory.saved'));
    }
}
