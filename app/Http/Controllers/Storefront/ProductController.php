<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CatalogRequest;
use App\Models\Product;
use App\Services\Catalog\CatalogFilterData;
use App\Services\Catalog\ProductCatalogQuery;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(
        CatalogRequest $request,
        ProductCatalogQuery $catalog,
        CatalogFilterData $filterData,
    ): View {
        $filters = $request->validated();
        $products = $catalog->paginate($filters);
        $options = $filterData->build();

        $hasActiveFilters = $this->hasFilters($request);
        $canonical = $request->integer('page', 1) > 1 && ! $hasActiveFilters
            ? $products->url($products->currentPage())
            : route('products.index');

        return view('storefront.products.index', [
            'products' => $products,
            'filters' => $filters,
            'options' => $options,
            'canonical' => $canonical,
            'robots' => $hasActiveFilters ? 'noindex,follow' : 'index,follow',
            'pageTitle' => filled($filters['q'] ?? null)
                ? __('storefront.catalog.search_title', ['term' => $filters['q']])
                : __('storefront.catalog.all_products'),
            'metaDescription' => __('storefront.catalog.meta_description'),
        ]);
    }

    public function show(
        Request $request,
        Product $product,
        WhatsAppService $whatsApp,
    ): View {
        abort_unless($product->is_active, 404);

        $product->load([
            'category.parent',
            'images',
            'variants',
        ]);

        $variants = $product->variants
            ->where('is_active', true)
            ->values()
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'colorHex' => $variant->color_hex,
                'stock' => (int) $variant->stock_quantity,
                'price' => $variant->price_override !== null ? (float) $variant->price_override : (float) $product->price,
                'priceFormatted' => money($variant->price_override !== null ? $variant->price_override : $product->price),
            ]);

        $gallery = $product->images
            ->map(fn ($image) => [
                'src' => media_url($image->path),
                'srcset' => media_srcset($image->path),
                'alt' => $image->alt_text ?: $product->name,
                'full' => media_url($image->path),
            ])
            ->values();

        if ($gallery->isEmpty()) {
            $gallery = collect([[
                'src' => null,
                'srcset' => null,
                'alt' => $product->name,
                'full' => null,
            ]]);
        }

        $relatedProducts = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest('updated_at')
            ->limit(4)
            ->get();

        $whatsappConfigured = $whatsApp->isConfigured();

        $modelUrl = null;
        $modelExtension = strtolower(pathinfo((string) $product->model_3d_path, PATHINFO_EXTENSION));
        $mediaDisk = config('store.media_disk', 'public');
        $modelDisk = Storage::disk($mediaDisk);

        if (
            in_array($modelExtension, ['glb', 'gltf'], true)
            && $modelDisk->exists($product->model_3d_path)
        ) {
            $modelUrl = $modelDisk->url($product->model_3d_path);

            // model-viewer fetches the file, so keep local-disk URLs same-origin.
            if (config("filesystems.disks.{$mediaDisk}.driver") === 'local') {
                $modelUrl = parse_url($modelUrl, PHP_URL_PATH) ?: $modelUrl;
            }
        }

        return view('storefront.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'gallery' => $gallery,
            'variants' => $variants,
            'whatsappConfigured' => $whatsappConfigured,
            'modelUrl' => $modelUrl,
            'orderUrl' => route('products.order', $product),
            'canonical' => $product->url,
            'pageTitle' => $product->seo_title ?: $product->name.' | '.store_setting('store_name'),
            'metaDescription' => $product->seo_description
                ?: str($product->short_description ?: $product->description)->stripTags()->limit(155)->toString(),
            'ogImage' => $gallery->first()['src'] ?? null,
        ]);
    }

    private function hasFilters(Request $request): bool
    {
        $keys = ['q', 'category', 'min_price', 'max_price', 'sizes', 'colors', 'availability', 'featured', 'on_sale', 'sort'];

        return collect($keys)->contains(fn (string $key) => $request->query($key) !== null && $request->query($key) !== []);
    }
}
