<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductImagesRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class ProductImageController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function store(ProductImagesRequest $request, Product $product): RedirectResponse
    {
        $sort = (int) $product->images()->max('sort_order');
        $storedPaths = [];

        try {
            foreach ($request->file('images', []) as $file) {
                $path = $this->media->storeImage($file, 'products');
                $storedPaths[] = $path;
                $sort++;

                $product->images()->create([
                    'path' => $path,
                    'alt_text' => null,
                    'sort_order' => $sort,
                ]);
            }
        } catch (RuntimeException) {
            foreach ($storedPaths as $path) {
                $this->media->delete($path);
            }

            return back()->withErrors(['images' => __('admin.products.image_upload_failed')]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.images_uploaded'));
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless((int) $image->product_id === (int) $product->id, 404);

        $image->delete();
        $this->media->delete($image->path);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.products.image_deleted'));
    }
}
