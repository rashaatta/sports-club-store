<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredCategories = Category::query()
            ->active()
            ->featured()
            ->root()
            ->ordered()
            ->limit(4)
            ->get();

        if ($featuredCategories->isEmpty()) {
            $featuredCategories = Category::query()
                ->active()
                ->root()
                ->ordered()
                ->limit(4)
                ->get();
        }

        $featuredProducts = Product::query()
            ->active()
            ->featured()
            ->with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $newArrivals = Product::query()
            ->active()
            ->newArrivals()
            ->with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $offers = Product::query()
            ->active()
            ->discounted()
            ->with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest('updated_at')
            ->limit(4)
            ->get();

        $banners = Banner::query()
            ->active()
            ->placement('home_promo')
            ->ordered()
            ->limit(2)
            ->get();

        return view('storefront.home', [
            'featuredCategories' => $featuredCategories,
            'featuredProducts' => $featuredProducts,
            'newArrivals' => $newArrivals,
            'offers' => $offers,
            'banners' => $banners,
        ]);
    }
}
