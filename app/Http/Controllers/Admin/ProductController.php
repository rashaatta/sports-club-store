<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\InventoryService;
use App\Services\MediaService;
use App\Support\Slugger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
        private readonly InventoryService $inventory,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = (int) $request->query('category', 0);
        $status = (string) $request->query('status', 'all');

        $categoryIds = [];

        if ($categoryId > 0) {
            $category = Category::query()->find($categoryId);

            if ($category !== null) {
                $categoryIds = [$category->id, ...$category->descendantIds()];
            }
        }

        $products = Product::query()
            ->with(['category:id,name,parent_id', 'images'])
            ->withStockTotals()
            ->search($search)
            ->when($categoryIds !== [], fn ($query) => $query->whereIn('category_id', $categoryIds))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($status === 'featured', fn ($query) => $query->where('is_featured', true))
            ->when($status === 'new', fn ($query) => $query->where('is_new_arrival', true))
            ->latest('updated_at')
            ->paginate(config('store.pagination.admin', 15))
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $this->categoryOptions(),
            'search' => $search,
            'categoryId' => $categoryId,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $imageFiles = $request->file('images', []);
        $modelFile = $request->file('model_3d');
        unset($data['images'], $data['image_alts'], $data['model_3d']);

        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Slugger::unique($data['name'], Product::class);
        $data['stock_quantity'] = (int) ($data['stock_quantity'] ?? 0);
        $data['specifications'] = filled($data['specifications'] ?? []) ? $data['specifications'] : null;

        $storedPaths = [];
        $storedModel = null;
        $product = null;

        try {
            if ($modelFile !== null) {
                $storedModel = $this->media->storeModel($modelFile);
                $data['model_3d_path'] = $storedModel;
            }

            DB::transaction(function () use ($data, $imageFiles, &$storedPaths, &$product): void {
                $product = Product::query()->create($data);

                foreach ($imageFiles as $index => $file) {
                    $path = $this->media->storeImage($file, 'products');
                    $storedPaths[] = $path;

                    $product->images()->create([
                        'path' => $path,
                        'alt_text' => null,
                        'sort_order' => $index,
                    ]);
                }
            });
        } catch (RuntimeException) {
            foreach ($storedPaths as $path) {
                $this->media->delete($path);
            }

            if ($storedModel !== null) {
                $this->media->delete($storedModel);
            }

            return back()->withInput()->withErrors(['images' => __('admin.products.image_upload_failed')]);
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->media->delete($path);
            }

            if ($storedModel !== null) {
                $this->media->delete($storedModel);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.created'));
    }

    public function edit(Product $product): View
    {
        $product->load([
            'images',
            'variants' => fn ($query) => $query->withCount('inventoryAdjustments'),
            'category.parent',
        ]);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $imageFiles = $request->file('images', []);
        $modelFile = $request->file('model_3d');
        unset($data['images'], $data['image_alts'], $data['model_3d']);

        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Slugger::unique($data['name'], Product::class, $product->id);
        $data['specifications'] = filled($data['specifications'] ?? []) ? $data['specifications'] : null;

        $stockInput = array_key_exists('stock_quantity', $data)
            ? (int) ($data['stock_quantity'] ?? 0)
            : null;
        unset($data['stock_quantity']);

        $oldModel = $product->model_3d_path;
        $newModel = null;
        $storedPaths = [];

        try {
            if ($modelFile !== null) {
                $newModel = $this->media->storeModel($modelFile);
                $data['model_3d_path'] = $newModel;
            }

            DB::transaction(function () use ($product, $data, $imageFiles, $stockInput, &$storedPaths, $request): void {
                $product->update($data);

                $hasVariants = $product->variants()->exists();
                $currentStock = (int) ($product->stock_quantity ?? 0);

                if (! $hasVariants && $stockInput !== null && $stockInput !== $currentStock) {
                    $this->inventory->adjust(
                        $product,
                        null,
                        'set',
                        $stockInput,
                        'correction',
                        __('admin.products.stock_note_product_form'),
                        $request->user(),
                    );
                }

                $sort = (int) $product->images()->max('sort_order');

                foreach ($imageFiles as $file) {
                    $path = $this->media->storeImage($file, 'products');
                    $storedPaths[] = $path;
                    $sort++;

                    $product->images()->create([
                        'path' => $path,
                        'alt_text' => null,
                        'sort_order' => $sort,
                    ]);
                }
            });
        } catch (RuntimeException) {
            foreach ($storedPaths as $path) {
                $this->media->delete($path);
            }

            if ($newModel !== null) {
                $this->media->delete($newModel);
            }

            return back()->withInput()->withErrors(['images' => __('admin.products.image_upload_failed')]);
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->media->delete($path);
            }

            if ($newModel !== null) {
                $this->media->delete($newModel);
            }

            throw $exception;
        }

        if ($newModel !== null && $oldModel !== null) {
            $this->media->delete($oldModel);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->inventoryAdjustments()->exists()) {
            return back()->with('error', __('admin.products.delete_blocked_history'));
        }

        $imagePaths = ProductImage::query()
            ->where('product_id', $product->id)
            ->pluck('path')
            ->all();
        $modelPath = $product->model_3d_path;

        DB::transaction(fn () => $product->delete());

        foreach ($imagePaths as $path) {
            $this->media->delete($path);
        }

        if ($modelPath !== null) {
            $this->media->delete($modelPath);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('status', __('admin.products.deleted'));
    }

    /**
     * @return array<int, string>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->with('parent:id,name')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id'])
            ->mapWithKeys(fn (Category $category) => [
                $category->id => ($category->parent ? $category->parent->name.' / ' : '').$category->name,
            ])
            ->all();
    }
}
