<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class BannerController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(): View
    {
        $banners = Banner::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(config('store.pagination.admin', 15));

        return view('admin.banners.index', [
            'banners' => $banners,
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);
        $data['image_path'] = null;

        try {
            $data['image_path'] = $this->media->storeImage($request->file('image'), 'banners');
            $banner = Banner::query()->create($data);
        } catch (RuntimeException) {
            if ($data['image_path'] !== null) {
                $this->media->delete($data['image_path']);
            }

            return back()->withInput()->withErrors(['image' => __('admin.banners.image_upload_failed')]);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('status', __('admin.banners.created'));
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
        ]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        $oldImage = $banner->image_path;
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $this->media->storeImage($request->file('image'), 'banners');
                $data['image_path'] = $newImage;
            }

            $banner->update($data);
        } catch (RuntimeException) {
            if ($newImage !== null) {
                $this->media->delete($newImage);
            }

            return back()->withInput()->withErrors(['image' => __('admin.banners.image_upload_failed')]);
        }

        if ($newImage !== null && $oldImage !== null) {
            $this->media->delete($oldImage);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('status', __('admin.banners.updated'));
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $image = $banner->image_path;
        $banner->delete();

        if ($image !== null) {
            $this->media->delete($image);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('status', __('admin.banners.deleted'));
    }
}
