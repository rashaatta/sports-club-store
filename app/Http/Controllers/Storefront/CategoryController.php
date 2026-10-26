<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CatalogRequest;
use App\Models\Category;
use App\Services\Catalog\CatalogFilterData;
use App\Services\Catalog\ProductCatalogQuery;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(
        CatalogRequest $request,
        int|string $category,
        ProductCatalogQuery $catalog,
        CatalogFilterData $filterData,
    ): View {
        $categoryModel = Category::query()
            ->active()
            ->with(['parent.parent', 'children' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
            ->where('slug', $category)
            ->firstOrFail();

        $filters = $request->validated();
        $products = $catalog->paginate($filters, $categoryModel);
        $options = $filterData->build();

        $ancestors = Category::query()
            ->whereIn('id', $categoryModel->ancestorIds())
            ->get()
            ->keyBy('id');

        $breadcrumb = collect(array_reverse($categoryModel->ancestorIds()))
            ->map(fn (int $id) => $ancestors->get($id))
            ->filter()
            ->push($categoryModel);

        $hasActiveFilters = $this->hasFilters($request);
        $canonical = $request->integer('page', 1) > 1 && ! $hasActiveFilters
            ? $products->url($products->currentPage())
            : route('categories.show', $categoryModel);

        return view('storefront.categories.show', [
            'category' => $categoryModel,
            'breadcrumb' => $breadcrumb,
            'products' => $products,
            'filters' => $filters,
            'options' => $options,
            'canonical' => $canonical,
            'robots' => $hasActiveFilters ? 'noindex,follow' : 'index,follow',
            'pageTitle' => $categoryModel->seo_title ?: $categoryModel->name.' | '.store_setting('store_name'),
            'metaDescription' => $categoryModel->seo_description
                ?: str($categoryModel->description)->stripTags()->limit(155)->toString(),
        ]);
    }

    private function hasFilters(CatalogRequest $request): bool
    {
        $keys = ['q', 'min_price', 'max_price', 'sizes', 'colors', 'availability', 'featured', 'on_sale', 'sort'];

        return collect($keys)->contains(fn (string $key) => $request->query($key) !== null && $request->query($key) !== []);
    }
}
