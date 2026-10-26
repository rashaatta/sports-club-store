<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $page = Page::query()->active()->where('slug', 'about-us')->firstOrFail();

        return view('storefront.pages.about', [
            'page' => $page,
            'pageTitle' => $page->meta_title ?: $page->title.' | '.store_setting('store_name'),
            'metaDescription' => $page->meta_description ?: str($page->safe_content)->stripTags()->limit(155)->toString(),
            'canonical' => route('about'),
        ]);
    }

    public function show(int|string $page): View|RedirectResponse
    {
        $pageModel = Page::query()->active()->where('slug', $page)->firstOrFail();

        if ($pageModel->slug === 'about-us') {
            return redirect()->route('about', status: 301);
        }

        return view('storefront.pages.show', [
            'page' => $pageModel,
            'pageTitle' => $pageModel->meta_title ?: $pageModel->title.' | '.store_setting('store_name'),
            'metaDescription' => $pageModel->meta_description ?: str($pageModel->safe_content)->stripTags()->limit(155)->toString(),
            'canonical' => route('pages.show', $pageModel),
        ]);
    }

    public function faq(): View
    {
        $faqs = Faq::query()->active()->ordered()->get();

        return view('storefront.pages.faq', [
            'faqs' => $faqs,
            'pageTitle' => __('storefront.faq.title').' | '.store_setting('store_name'),
            'metaDescription' => __('storefront.faq.meta_description'),
            'canonical' => route('faq'),
        ]);
    }
}
