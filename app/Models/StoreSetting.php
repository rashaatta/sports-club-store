<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    private const CACHE_KEY = 'store.settings';

    /** @var list<string> */
    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }

    /**
     * @return array<string, array{type: string, default: string}>
     */
    public static function definitions(): array
    {
        $fields = [];

        foreach (config('store.settings', []) as $group) {
            $fields += $group['fields'] ?? [];
        }

        return $fields;
    }

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return array_map(fn (array $field) => (string) ($field['default'] ?? ''), self::definitions());
    }

    /**
     * @return array<string, string>
     */
    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return array_replace(
                self::defaults(),
                self::query()->pluck('value', 'key')->map(fn ($value) => (string) $value)->all(),
            );
        });
    }

    public static function get(string $key, ?string $default = null): string
    {
        $values = self::values();

        if (array_key_exists($key, $values) && $values[$key] !== '') {
            return $values[$key];
        }

        return $default ?? (self::defaults()[$key] ?? '');
    }

    public static function bool(string $key): bool
    {
        return in_array(self::get($key), ['1', 'true', 'on'], true);
    }

    public static function set(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
