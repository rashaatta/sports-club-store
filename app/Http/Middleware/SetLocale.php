<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = array_keys((array) config('store.locales', []));
        $fallback = (string) config('app.locale');
        $locale = $request->cookie('locale') ?? $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $locale = in_array($fallback, $locales, true) ? $fallback : ($locales[0] ?? 'en');
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
