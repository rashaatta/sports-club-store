<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\ContactController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\SitemapController;
use App\Http\Controllers\Storefront\WhatsAppOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', array_keys((array) config('store.locales', [])))
    ->name('locale.switch');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/order', [WhatsAppOrderController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('products.order');

Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
