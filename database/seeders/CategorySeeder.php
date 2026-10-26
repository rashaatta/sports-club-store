<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Concerns\ImportsSeedMedia;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use ImportsSeedMedia;

    public function run(): void
    {
        $roots = [
            'footwear' => [
                'name' => 'أحذية رياضية',
                'description' => 'أحذية جري وتدريب وكرة قدم تجمع بين الثبات والراحة والخفة.',
                'image' => 'category-footwear.webp',
                'order' => 1,
                'seo_description' => 'تسوّق أحذية الجري وكرة القدم والتدريب من SQUAD، واطلب مقاسك عبر واتساب.',
            ],
            'jerseys' => [
                'name' => 'قمصان وملابس',
                'description' => 'تيشيرتات وقمصان فرق بخامات تتنفس وتتحمل التدريب والاستخدام اليومي.',
                'image' => 'category-jerseys.webp',
                'order' => 2,
                'seo_description' => 'قمصان وتيشيرتات رياضية بخامات مريحة، مقاسات متعددة وطلب مباشر عبر واتساب.',
            ],
            'training' => [
                'name' => 'أطقم تدريب',
                'description' => 'أطقم رياضية وشورتات مصممة للحركة الكاملة قبل وبعد التمرين.',
                'image' => 'category-training.webp',
                'order' => 3,
                'seo_description' => 'أطقم تدريب وشورتات رياضية عملية، اختر المقاس واللون وأتمم طلبك عبر واتساب.',
            ],
            'accessories' => [
                'name' => 'إكسسوارات',
                'description' => 'حقائب وقبعات وزجاجات مياه تكمل عدتك الرياضية اليومية.',
                'image' => 'category-accessories.webp',
                'order' => 4,
                'seo_description' => 'إكسسوارات رياضية مختارة من الحقائب والقبعات وزجاجات المياه لدى SQUAD.',
            ],
        ];

        $rootModels = [];

        foreach ($roots as $slug => $data) {
            $rootModels[$slug] = $this->seedCategory([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'display_order' => $data['order'],
                'is_active' => true,
                'is_featured' => true,
                'seo_title' => $data['name'].' | SQUAD',
                'seo_description' => $data['seo_description'],
            ], $data['image']);
        }

        $children = [
            ['parent' => 'footwear', 'slug' => 'running-shoes', 'name' => 'أحذية جري', 'order' => 1],
            ['parent' => 'footwear', 'slug' => 'football-boots', 'name' => 'أحذية كرة القدم', 'order' => 2],
            ['parent' => 'jerseys', 'slug' => 'tshirts', 'name' => 'قمصان وتيشيرتات', 'order' => 1],
            ['parent' => 'jerseys', 'slug' => 'team-jerseys', 'name' => 'قمصان فرق', 'order' => 2],
            ['parent' => 'training', 'slug' => 'tracksuits', 'name' => 'أطقم رياضية', 'order' => 1],
            ['parent' => 'training', 'slug' => 'shorts', 'name' => 'شورتات تدريب', 'order' => 2],
            ['parent' => 'accessories', 'slug' => 'bags', 'name' => 'حقائب رياضية', 'order' => 1],
            ['parent' => 'accessories', 'slug' => 'caps-bottles', 'name' => 'قبعات وزجاجات', 'order' => 2],
        ];

        foreach ($children as $child) {
            $this->seedCategory([
                'parent_id' => $rootModels[$child['parent']]->id,
                'name' => $child['name'],
                'slug' => $child['slug'],
                'description' => null,
                'display_order' => $child['order'],
                'is_active' => true,
                'is_featured' => false,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function seedCategory(array $attributes, ?string $image = null): Category
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => $attributes['slug']],
            $attributes,
        );

        if ($image !== null && blank($category->image_path)) {
            $category->forceFill([
                'image_path' => $this->importSeedImage($image, 'categories'),
            ])->save();
        }

        return $category;
    }
}
