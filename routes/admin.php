<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:view_admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)
            ->middleware('can:view_dashboard')
            ->name('dashboard');

        Route::get('analytics', [AnalyticsController::class, 'index'])
            ->middleware('can:view_analytics')
            ->name('analytics.index');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');

        Route::middleware('can:manage_products')->group(function () {
            Route::resource('products', ProductController::class)->except(['show']);

            Route::scopeBindings()->group(function () {
                Route::post('products/{product}/images', [ProductImageController::class, 'store'])
                    ->name('products.images.store');
                Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
                    ->name('products.images.destroy');

                Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
                    ->name('products.variants.store');
                Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])
                    ->name('products.variants.update');
                Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
                    ->name('products.variants.destroy');
            });
        });

        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->middleware('can:manage_categories');

        Route::middleware('can:manage_inventory')->group(function () {
            Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
            Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
            Route::post('inventory/{product}', [InventoryController::class, 'store'])->name('inventory.store');
        });

        Route::middleware('can:manage_content')->group(function () {
            Route::resource('pages', PageController::class)->except(['show']);
            Route::resource('banners', BannerController::class)->except(['show']);
            Route::resource('faqs', FaqController::class)->except(['show']);
        });

        Route::middleware('can:view_messages')->group(function () {
            Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
            Route::patch('messages/{message}/read', [ContactMessageController::class, 'markRead'])->name('messages.read');
            Route::patch('messages/{message}/unread', [ContactMessageController::class, 'markUnread'])->name('messages.unread');
            Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        });

        Route::resource('users', UserController::class)
            ->except(['show'])
            ->middleware('can:manage_users');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media', [MediaController::class, 'destroy'])->name('media.destroy');
    });
