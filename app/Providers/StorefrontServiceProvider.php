<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class StorefrontServiceProvider extends ServiceProvider
{
    private ?Collection $navCategories = null;

    private ?Collection $footerPages = null;

    public function boot(): void
    {
        View::composer(['components.layouts.storefront', 'storefront.*'], function ($view) {
            $navCategories = $this->navCategories ??= Category::query()
                ->active()
                ->root()
                ->ordered()
                ->with(['children' => fn ($query) => $query->where('is_active', true)])
                ->get();

            $footerPages = $this->footerPages ??= Page::query()
                ->active()
                ->footer()
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();

            $whatsApp = app(WhatsAppService::class);

            $view->with([
                'navCategories' => $navCategories,
                'footerPages' => $footerPages,
                'whatsappUrl' => $whatsApp->contactUrl($whatsApp->contactMessage()),
                'whatsappConfigured' => $whatsApp->isConfigured(),
            ]);
        });

        View::composer('components.layouts.admin', function ($view) {
            $unreadMessages = auth()->check() && auth()->user()->can('view_messages')
                ? ContactMessage::query()->unread()->count()
                : 0;

            $view->with('unreadMessages', $unreadMessages);
        });
    }
}
