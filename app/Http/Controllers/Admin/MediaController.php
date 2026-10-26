<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaUploadRequest;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StoreSetting;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class MediaController extends Controller
{
    private const FOLDERS = ['products', 'categories', 'banners', 'settings', 'branding', 'pages', 'media', 'models'];

    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): View
    {
        abort_unless(
            $request->user()?->can('manage_products') || $request->user()?->can('manage_content'),
            403,
        );

        $disk = Storage::disk(config('store.media_disk', 'public'));
        $search = trim((string) $request->query('q', ''));
        $folder = (string) $request->query('folder', '');

        $files = collect($disk->allFiles())
            ->filter(fn (string $path) => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif'], true))
            ->filter(fn (string $path) => ! str_starts_with(basename($path), '.'))
            ->filter(fn (string $path) => $folder === '' || str_starts_with($path, $folder.'/'))
            ->filter(fn (string $path) => $search === '' || str_contains(basename($path), $search))
            ->values();

        $folders = collect($disk->allFiles())
            ->map(fn (string $path) => explode('/', $path)[0])
            ->filter(fn (string $directory) => in_array($directory, self::FOLDERS, true))
            ->unique()
            ->sort()
            ->values();

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 24;
        $items = $files
            ->forPage($page, $perPage)
            ->map(function (string $path) use ($disk): array {
                return [
                    'path' => $path,
                    'name' => basename($path),
                    'folder' => explode('/', $path)[0],
                    'url' => $disk->url($path),
                    'size' => $disk->size($path),
                    'modified' => $disk->lastModified($path),
                ];
            })
            ->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $files->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('admin.media.index', [
            'media' => $paginator,
            'folders' => $folders,
            'search' => $search,
            'folder' => $folder,
        ]);
    }

    public function store(MediaUploadRequest $request): RedirectResponse
    {
        try {
            $path = $this->media->storeImage($request->file('image'), 'media');
        } catch (RuntimeException) {
            return back()->withErrors(['image' => __('admin.media.upload_failed')]);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('status', __('admin.media.uploaded').' '.$path);
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort_unless(
            $request->user()?->can('manage_products') || $request->user()?->can('manage_content'),
            403,
        );

        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        $path = str_replace('\\', '/', $validated['path']);
        $folder = explode('/', $path)[0];

        abort_unless(
            in_array($folder, self::FOLDERS, true)
            && ! str_contains($path, '..')
            && preg_match('/^[A-Za-z0-9_\-\/.]+$/', $path) === 1,
            404,
        );

        $disk = Storage::disk(config('store.media_disk', 'public'));

        if (! $disk->exists($path)) {
            return back()->with('error', __('admin.media.not_found'));
        }

        if ($this->isInUse($path)) {
            return back()->with('error', __('admin.media.in_use'));
        }

        try {
            $this->media->delete($path);
        } catch (Throwable) {
            return back()->with('error', __('admin.media.delete_failed'));
        }

        return back()->with('status', __('admin.media.deleted'));
    }

    private function isInUse(string $path): bool
    {
        return ProductImage::query()->where('path', $path)->exists()
            || Category::query()->where('image_path', $path)->exists()
            || Banner::query()->where('image_path', $path)->exists()
            || Product::query()->where('model_3d_path', $path)->exists()
            || StoreSetting::query()->where('value', $path)->exists()
            || Page::query()->where('content', 'like', '%'.$path.'%')->exists();
    }
}
