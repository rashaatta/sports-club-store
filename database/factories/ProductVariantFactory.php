<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => 'VAR-'.strtoupper(Str::random(10)),
            'size' => fake()->randomElement(['S', 'M', 'L', 'XL', '40', '41', '42']),
            'color' => fake()->randomElement(['أسود', 'أبيض', 'رمادي']),
            'color_hex' => fake()->randomElement(['#111111', '#f5f5f5', '#808080']),
            'price_override' => null,
            'stock_quantity' => 10,
            'is_active' => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 0]);
    }
}
