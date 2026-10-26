<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        return response()->view('storefront.sitemap', [
            'products' => Product::query()->active()->latest('updated_at')->get(['id', 'slug', 'updated_at']),
            'categories' => Category::query()->active()->ordered()->get(['id', 'slug', 'updated_at']),
            'pages' => Page::query()->active()->orderBy('sort_order')->get(['id', 'slug', 'updated_at']),
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /login',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Allow: /',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
