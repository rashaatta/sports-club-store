<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductCatalogQuery
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, ?Category $categoryContext = null): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->withCount('variants');

        $category = $categoryContext ?? $this->resolveCategory($filters['category'] ?? null);
        $unknownCategoryFilter = filled($filters['category'] ?? null) && $category === null;

        if ($unknownCategoryFilter) {
            $query->whereRaw('1 = 0');
        } elseif ($category !== null) {
            $query->whereIn('category_id', [$category->id, ...$category->descendantIds()]);
        }

        if (filled($filters['q'] ?? null)) {
            $query->search($filters['q']);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== null) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sizes = array_filter((array) ($filters['sizes'] ?? []));
        $colors = array_filter((array) ($filters['colors'] ?? []));

        if ($sizes !== [] || $colors !== []) {
            $query->whereHas('variants', function (Builder $variant) use ($sizes, $colors) {
                $variant->where('is_active', true);

                if ($sizes !== []) {
                    $variant->whereIn('size', $sizes);
                }

                if ($colors !== []) {
                    $variant->whereIn('color', $colors);
                }
            });
        }

        if (($filters['availability'] ?? null) === 'in_stock') {
            $query->inStock();
        } elseif (($filters['availability'] ?? null) === 'out_of_stock') {
            $query->where(function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->whereDoesntHave('variants')
                        ->where(function (Builder $query) {
                            $query->whereNull('stock_quantity')->orWhere('stock_quantity', '<=', 0);
                        });
                })->orWhere(function (Builder $query) {
                    $query->whereHas('variants')
                        ->whereDoesntHave('variants', function (Builder $query) {
                            $query->where('is_active', true)->where('stock_quantity', '>', 0);
                        });
                });
            });
        }

        if (filter_var($filters['featured'] ?? false, FILTER_VALIDATE_BOOL)) {
            $query->featured();
        }

        if (filter_var($filters['on_sale'] ?? false, FILTER_VALIDATE_BOOL)) {
            $query->discounted();
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->latest('created_at')->latest('id'),
        };

        return $query
            ->paginate(config('store.pagination.products'))
            ->withQueryString();
    }

    private function resolveCategory(?string $slug): ?Category
    {
        if (blank($slug)) {
            return null;
        }

        return Category::query()->active()->where('slug', $slug)->first();
    }
}
