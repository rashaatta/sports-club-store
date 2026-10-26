<?php

use App\Models\StoreSetting;
use App\Support\Money;
use App\Support\Permission;
use Illuminate\Support\Facades\Storage;

if (! function_exists('store_setting')) {
    function store_setting(string $key, ?string $default = null): string
    {
        return StoreSetting::get($key, $default);
    }
}

if (! function_exists('store_boolean')) {
    function store_boolean(string $key): bool
    {
        return StoreSetting::bool($key);
    }
}

if (! function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Storage::disk(config('store.media_disk', 'public'))->url($path);
    }
}

if (! function_exists('media_srcset')) {
    function media_srcset(?string $path): ?string
    {
        static $cache = [];

        if (blank($path)) {
            return null;
        }

        if (array_key_exists($path, $cache)) {
            return $cache[$path];
        }

        $disk = Storage::disk(config('store.media_disk', 'public'));
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $base = $extension ? substr($path, 0, -(strlen($extension) + 1)) : $path;
        $widths = config('store.uploads.image_widths', [480, 960, 1920]);
        $sources = [];

        foreach ($widths as $width) {
            if ($width === 1920) {
                $sources[] = $disk->url($path).' 1920w';

                continue;
            }

            $candidate = $base.'-'.$width.($extension ? '.'.$extension : '');

            if ($disk->exists($candidate)) {
                $sources[] = $disk->url($candidate).' '.$width.'w';
            }
        }

        return $cache[$path] = $sources === [] ? null : implode(', ', $sources);
    }
}

if (! function_exists('store_dir')) {
    function store_dir(): string
    {
        return config('store.locales.'.app()->getLocale().'.dir', 'rtl');
    }
}

if (! function_exists('is_rtl')) {
    function is_rtl(): bool
    {
        return store_dir() === 'rtl';
    }
}

if (! function_exists('money')) {
    function money(float|int|string|null $amount): string
    {
        return Money::format($amount);
    }
}

if (! function_exists('safe_public_url')) {
    function safe_public_url(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return url($url);
        }

        return preg_match('#^https?://#i', $url) === 1 ? $url : null;
    }
}

if (! function_exists('catalog_filter_url')) {
    function catalog_filter_url(string $key, ?string $value = null): string
    {
        $query = request()->query();

        if ($value === null) {
            unset($query[$key]);
        } elseif (is_array($query[$key] ?? null)) {
            $remaining = array_values(array_diff($query[$key], [$value]));
            $remaining === [] ? $query = array_diff_key($query, [$key => true]) : $query[$key] = $remaining;
        } else {
            unset($query[$key]);
        }

        unset($query['page']);

        return url()->current().($query !== [] ? '?'.http_build_query($query) : '');
    }
}

if (! function_exists('catalog_sort_url')) {
    function catalog_sort_url(string $sort): string
    {
        return url()->current().'?'.http_build_query(
            array_merge(request()->except(['sort', 'page']), ['sort' => $sort]),
        );
    }
}

if (! function_exists('can_permission')) {
    function can_permission(string $permission): bool
    {
        return auth()->check() && auth()->user()->can($permission);
    }
}

if (! function_exists('permission_names')) {
    /**
     * @return list<string>
     */
    function permission_names(): array
    {
        return Permission::all();
    }
}
