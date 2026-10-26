<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

class CatalogFilterData
{
    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $categories = Category::query()
            ->active()
            ->root()
            ->ordered()
            ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
            ->get();

        $sizes = ProductVariant::query()
            ->where('is_active', true)
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->whereHas('product', fn ($query) => $query->active())
            ->distinct()
            ->pluck('size')
            ->sort(fn ($a, $b) => strnatcasecmp((string) $a, (string) $b))
            ->values();

        $colors = ProductVariant::query()
            ->where('is_active', true)
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->whereHas('product', fn ($query) => $query->active())
            ->get(['color', 'color_hex'])
            ->unique('color')
            ->sortBy('color')
            ->values()
            ->map(fn (ProductVariant $variant) => [
                'name' => $variant->color,
                'hex' => $variant->color_hex,
            ]);

        $bounds = Product::query()
            ->active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'categories' => $categories,
            'sizes' => $sizes,
            'colors' => $colors,
            'min_price' => (int) floor((float) ($bounds->min_price ?? 0)),
            'max_price' => (int) ceil((float) ($bounds->max_price ?? 0)),
        ];
    }
}
