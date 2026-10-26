<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\MediaService;
use App\Support\Slugger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class CategoryController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->with('parent:id,name')
            ->withCount('products')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('parent_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(config('store.pagination.admin', 15))
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'parents' => $this->parentOptions(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['parent_id'] ??= null;
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Slugger::unique($data['name'], Category::class);

        unset($data['image'], $data['remove_image']);

        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $this->media->storeImage($request->file('image'), 'categories');
                $data['image_path'] = $newImage;
            }

            $category = Category::query()->create($data);
        } catch (RuntimeException) {
            if ($newImage !== null) {
                $this->media->delete($newImage);
            }

            return back()->withInput()->withErrors(['image' => __('admin.categories.image_upload_failed')]);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('status', __('admin.categories.created'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => $this->parentOptions($category),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['parent_id'] ??= null;
        $data['slug'] = filled($data['slug'] ?? null)
            ? $data['slug']
            : Slugger::unique($data['name'], Category::class, $category->id);

        if ($data['parent_id'] !== null && $category->children()->exists()) {
            return back()->withInput()->withErrors([
                'parent_id' => __('admin.categories.cannot_become_child'),
            ]);
        }

        unset($data['image'], $data['remove_image']);

        $oldImage = $category->image_path;
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $this->media->storeImage($request->file('image'), 'categories');
                $data['image_path'] = $newImage;
            } elseif ($request->boolean('remove_image')) {
                $data['image_path'] = null;
            }

            DB::transaction(fn () => $category->update($data));
        } catch (RuntimeException) {
            if ($newImage !== null) {
                $this->media->delete($newImage);
            }

            return back()->withInput()->withErrors(['image' => __('admin.categories.image_upload_failed')]);
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->media->delete($newImage);
            }

            throw $exception;
        }

        if ($oldImage !== null && $category->image_path !== $oldImage) {
            $this->media->delete($oldImage);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('status', __('admin.categories.updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->with('error', __('admin.categories.delete_blocked_children'));
        }

        if ($category->products()->exists()) {
            return back()->with('error', __('admin.categories.delete_blocked_products'));
        }

        $image = $category->image_path;
        $category->delete();

        if ($image !== null) {
            $this->media->delete($image);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('status', __('admin.categories.deleted'));
    }

    /**
     * @return array<int, string>
     */
    private function parentOptions(?Category $current = null): array
    {
        return Category::query()
            ->root()
            ->ordered()
            ->when($current !== null, fn ($query) => $query->whereKeyNot($current->id))
            ->get(['id', 'name'])
            ->mapWithKeys(fn (Category $category) => [$category->id => $category->name])
            ->all();
    }
}
