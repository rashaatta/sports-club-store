<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AnalyticsService $analytics): View
    {
        $user = auth()->user();

        $activeProducts = Product::query()
            ->active()
            ->with('category:id,name')
            ->withStockTotals()
            ->get();

        $lowStockProducts = $activeProducts
            ->filter(fn (Product $product) => $product->is_low_stock)
            ->sortBy('total_stock')
            ->take(5)
            ->values();

        $outOfStockCount = $activeProducts
            ->filter(fn (Product $product) => ! $product->is_in_stock)
            ->count();

        $lowStockCount = $activeProducts
            ->filter(fn (Product $product) => $product->is_low_stock)
            ->count();

        $clicks = $user?->can('view_analytics')
            ? $analytics->whatsappClicksSummary(7)
            : null;

        $topProducts = collect();

        if ($user?->can('view_analytics')) {
            $topProducts = AnalyticsEvent::query()
                ->where('event_name', 'whatsapp_order_click')
                ->where('created_at', '>=', now()->subDays(30)->startOfDay())
                ->whereNotNull('product_id')
                ->select('product_id', DB::raw('COUNT(*) as clicks'))
                ->groupBy('product_id')
                ->orderByDesc('clicks')
                ->with(['product' => fn ($query) => $query->with('category')])
                ->limit(5)
                ->get();
        }

        $unreadMessages = $user?->can('view_messages')
            ? ContactMessage::query()->unread()->count()
            : null;

        $recentMessages = $user?->can('view_messages')
            ? ContactMessage::query()->latest()->limit(4)->get()
            : collect();

        $recentAdjustments = $user?->can('manage_inventory')
            ? InventoryAdjustment::query()
                ->with(['product:id,name,slug', 'variant:id,size,color', 'user:id,name'])
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('admin.dashboard', [
            'activeProductCount' => $activeProducts->count(),
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'lowStockProducts' => $lowStockProducts,
            'categoryCount' => Category::query()->active()->count(),
            'clicks' => $clicks,
            'topProducts' => $topProducts,
            'unreadMessages' => $unreadMessages,
            'recentMessages' => $recentMessages,
            'recentAdjustments' => $recentAdjustments,
        ]);
    }
}
