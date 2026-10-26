<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'منتج تجريبي '.fake()->unique()->numberBetween(1, 99999);
        $price = fake()->randomFloat(2, 99, 899);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'sku' => 'SKU-'.strtoupper(Str::random(8)),
            'brand' => null,
            'short_description' => 'منتج تجريبي لعرض تجربة المتجر.',
            'description' => 'وصف تجريبي كامل للمنتج يوضح استخداماته وخاماته.',
            'price' => $price,
            'compare_at_price' => null,
            'stock_quantity' => 20,
            'low_stock_threshold' => 5,
            'is_featured' => false,
            'is_new_arrival' => false,
            'is_active' => true,
            'specifications' => [
                ['label' => 'الخامة', 'value' => 'قماش تقني'],
                ['label' => 'العناية', 'value' => 'غسيل بارد'],
            ],
            'seo_title' => null,
            'seo_description' => null,
            'model_3d_path' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function newArrival(): static
    {
        return $this->state(fn () => ['is_new_arrival' => true]);
    }

    public function discounted(): static
    {
        return $this->state(fn (array $attributes) => [
            'compare_at_price' => (float) $attributes['price'] + 100,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 0]);
    }
}
