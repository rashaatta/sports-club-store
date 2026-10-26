<?php

namespace App\Models;

use App\Services\HtmlSanitizerService;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    public const FOOTER_CACHE_KEY = 'store.footer.pages';

    /** @var list<string> */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
        'show_in_footer',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::FOOTER_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::FOOTER_CACHE_KEY));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFooter(Builder $query): Builder
    {
        return $query->where('show_in_footer', true);
    }

    public function getSafeContentAttribute(): string
    {
        return app(HtmlSanitizerService::class)->clean($this->content);
    }
}
