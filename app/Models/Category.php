<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public const NAV_CACHE_KEY = 'store.nav.categories';

    /** @var list<string> */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image_path',
        'display_order',
        'is_active',
        'is_featured',
        'seo_title',
        'seo_description',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::NAV_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::NAV_CACHE_KEY));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'display_order' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('display_order')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * @return list<int>
     */
    public function descendantIds(): array
    {
        $childrenByParent = self::query()
            ->get(['id', 'parent_id'])
            ->groupBy(fn (Category $category) => $category->parent_id ?? 0);

        $ids = [];
        $walk = function (int $parentId) use (&$walk, &$ids, $childrenByParent): void {
            foreach ($childrenByParent->get($parentId, []) as $child) {
                $ids[] = $child->id;
                $walk($child->id);
            }
        };

        $walk($this->id);

        return $ids;
    }

    /**
     * @return list<int>
     */
    public function ancestorIds(): array
    {
        $ids = [];
        $category = $this;
        $guard = 0;

        while ($category->parent_id !== null && $guard < 20) {
            $ids[] = $category->parent_id;
            $category = self::query()->find($category->parent_id);

            if ($category === null) {
                break;
            }

            $guard++;
        }

        return $ids;
    }
}
