<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'فئة '.fake()->unique()->numberBetween(1, 9999);

        return [
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'description' => 'وصف تجريبي للفئة.',
            'image_path' => null,
            'display_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'is_featured' => false,
            'seo_title' => null,
            'seo_description' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
