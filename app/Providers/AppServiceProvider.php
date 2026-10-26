<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Permission;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach (Permission::all() as $permission) {
            Gate::define($permission, fn (User $user) => $user->role->has($permission));
        }

        Paginator::defaultView('vendor.pagination.store');
        Paginator::defaultSimpleView('vendor.pagination.simple');

        if (filled(config('store.trusted_proxies'))) {
            TrustProxies::at(config('store.trusted_proxies'));
        }

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
