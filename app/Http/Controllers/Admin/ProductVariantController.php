<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class ProductVariantController extends Controller
{
    public function store(ProductVariantRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['product_id'] = $product->id;
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['color_hex'] = filled($data['color_hex'] ?? null) ? $data['color_hex'] : null;
        $data['price_override'] = filled($data['price_override'] ?? null) ? $data['price_override'] : null;

        ProductVariant::query()->create($data);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.variant_added'));
    }

    public function update(ProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);

        $data = Arr::except($request->validated(), ['stock_quantity']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['color_hex'] = filled($data['color_hex'] ?? null) ? $data['color_hex'] : null;
        $data['price_override'] = filled($data['price_override'] ?? null) ? $data['price_override'] : null;

        $variant->update($data);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.variant_updated'));
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);

        if ($variant->inventoryAdjustments()->exists()) {
            return back()->with('error', __('admin.products.variant_delete_blocked'));
        }

        $variant->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.variant_deleted'));
    }
}
