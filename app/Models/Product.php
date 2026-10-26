<?php

namespace App\Models;

use App\Support\Money;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'brand',
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'stock_quantity',
        'low_stock_threshold',
        'is_featured',
        'is_new_arrival',
        'is_active',
        'specifications',
        'seo_title',
        'seo_description',
        'model_3d_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_active' => 'boolean',
            'specifications' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Specifications historically accepted both label => value maps and row lists.
     *
     * @return list<array{key: string, value: string}>
     */
    public function specificationRows(): array
    {
        $rows = [];

        foreach ((array) $this->specifications as $key => $value) {
            if (is_array($value)) {
                $label = trim((string) ($value['key'] ?? $value['label'] ?? ''));
                $text = trim((string) ($value['value'] ?? ''));
            } else {
                $label = is_string($key) ? trim($key) : '';
                $text = trim((string) $value);
            }

            if ($label !== '' && $text !== '') {
                $rows[] = ['key' => $label, 'value' => $text];
            }
        }

        return $rows;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('id');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeDiscounted(Builder $query): Builder
    {
        return $query->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', '%'.$term.'%')
                ->orWhere('sku', 'like', '%'.$term.'%')
                ->orWhere('brand', 'like', '%'.$term.'%')
                ->orWhere('short_description', 'like', '%'.$term.'%');
        });
    }

    public function scopeWithStockTotals(Builder $query): Builder
    {
        return $query
            ->withCount(['variants', 'activeVariants'])
            ->withSum(['activeVariants as active_variants_stock'], 'stock_quantity');
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereDoesntHave('variants')
                    ->where('stock_quantity', '>', 0);
            })->orWhereHas('variants', function (Builder $query) {
                $query->where('is_active', true)->where('stock_quantity', '>', 0);
            });
        });
    }

    public function usesVariants(): bool
    {
        if (array_key_exists('variants_count', $this->attributes)) {
            return (int) $this->attributes['variants_count'] > 0;
        }

        return $this->variants->isNotEmpty();
    }

    public function effectiveTotalStock(): int
    {
        if (array_key_exists('variants_count', $this->attributes)) {
            if ((int) $this->attributes['variants_count'] === 0) {
                return max(0, (int) ($this->stock_quantity ?? 0));
            }

            if (array_key_exists('active_variants_stock', $this->attributes)) {
                return max(0, (int) $this->attributes['active_variants_stock']);
            }
        }

        if ($this->usesVariants()) {
            return (int) $this->variants
                ->where('is_active', true)
                ->sum('stock_quantity');
        }

        return max(0, (int) ($this->stock_quantity ?? 0));
    }

    public function getTotalStockAttribute(): int
    {
        return $this->effectiveTotalStock();
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->effectiveTotalStock() > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        $stock = $this->effectiveTotalStock();

        return $stock > 0 && $stock <= max(1, (int) $this->low_stock_threshold);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        return Money::discountPercent($this->price, $this->compare_at_price);
    }

    public function getPriceFormattedAttribute(): string
    {
        return Money::format($this->price);
    }

    public function getCompareAtFormattedAttribute(): ?string
    {
        return $this->compare_at_price !== null ? Money::format($this->compare_at_price) : null;
    }

    public function getPrimaryImagePathAttribute(): ?string
    {
        return $this->images->first()?->path;
    }

    public function getUrlAttribute(): string
    {
        return route('products.show', $this);
    }
}
