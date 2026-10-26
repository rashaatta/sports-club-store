<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'redirect' => ['nullable', 'string', 'max:2048'],
        ]);

        $locales = array_keys((array) config('store.locales', []));
        $locale = (string) $request->route('locale');

        abort_unless(in_array($locale, $locales, true), 404);

        $request->session()->put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'lax');

        app()->setLocale($locale);

        $target = $this->localRedirect($data['redirect'] ?? null);

        if ($target !== null) {
            return redirect()->to($target);
        }

        return $request->user()?->can('view_admin')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }

    private function localRedirect(?string $target): ?string
    {
        if (! is_string($target) || $target === '') {
            return null;
        }

        if (! str_starts_with($target, '/') || str_starts_with($target, '//') || str_contains($target, '\\')) {
            return null;
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $target) === 1) {
            return null;
        }

        return $target;
    }
}
