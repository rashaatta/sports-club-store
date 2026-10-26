<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'صفحة '.fake()->unique()->numberBetween(1, 9999);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'content' => '<p>محتوى تجريبي للصفحة.</p>',
            'meta_title' => null,
            'meta_description' => null,
            'is_active' => true,
            'show_in_footer' => true,
            'sort_order' => 0,
        ];
    }
}
