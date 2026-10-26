<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\Concerns\ImportsSeedMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductSeeder extends Seeder
{
    use ImportsSeedMedia;

    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');

        $products = [
            [
                'category' => 'running-shoes',
                'name' => 'Aero Runner',
                'slug' => 'aero-runner',
                'sku' => 'SQ-FW-001',
                'short_description' => 'حذاء جري خفيف بنعل مرن يمنحك خطوة سلسة في التدريب اليومي.',
                'description' => "Aero Runner مصمم ليكون رفيقك في الجري اليومي والتمارين السريعة. شبك علوي خفيف يسمح بمرور الهواء، ونعل مطاطي مرن يمتص الصدمات ويحافظ على ثبات القدم.\n\nالجزء الداخلي مبطن يقلل الاحتكاك في الجلسات الطويلة، والتصميم منخفض الارتفاع يمنحك إحساسًا قريبًا من الأرض دون التضحية بالدعم.\n\nاختر مقاسك المعتاد، وإذا كنت بين مقاسين فاختر النصف الأكبر للراحة أثناء الجري.",
                'price' => 349,
                'compare_at_price' => 429,
                'low_stock_threshold' => 5,
                'is_featured' => true,
                'is_new_arrival' => true,
                'specifications' => [
                    'الخامة' => 'شبك هندسي قابل للتهوية',
                    'النعل' => 'مطاط مرن مع وسادة استجابة',
                    'الاستخدام' => 'الجري والتدريب اليومي',
                    'العناية' => 'مسح بقطعة قماش رطبة وتجفيف في الهواء',
                ],
                'seo_title' => 'Aero Runner | حذاء جري خفيف من SQUAD',
                'seo_description' => 'حذاء جري Aero Runner بشبك متهوي ونعل مرن. مقاسات وألوان متعددة، واطلب عبر واتساب.',
                'images' => ['product-runner.webp', 'product-runner-detail.webp'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'colors' => [
                    'BK' => ['name' => 'أسود', 'hex' => '#141414'],
                    'WH' => ['name' => 'أبيض', 'hex' => '#f1f1ec'],
                ],
                'stock' => ['BK' => [6, 4, 8, 7, 5, 3], 'WH' => [3, 0, 6, 2, 5, 4]],
                'age' => 2,
            ],
            [
                'category' => 'football-boots',
                'name' => 'Velocity FG',
                'slug' => 'velocity-fg',
                'sku' => 'SQ-FW-002',
                'short_description' => 'حذاء كرة قدم للأرضية العشبية الجافة بتصميم يدعم الثبات والانطلاق.',
                'description' => "Velocity FG صُمم للاعب الذي يحتاج ثباتًا سريعًا في الالتحامات وانطلاقة حاسمة نحو المرمى. بنية سفلية بمسامير مخروطية توفر تماسكًا على العشب الطبيعي الجاف.\n\nالجزء العلوي بطبقة خارجية صناعية سهلة التنظيف، مع منطقة كعب مدعمة تحافظ على استقرار القدم أثناء الجري.\n\nمناسب للمباريات والتدريبات المنتظمة على أرضية العشب الطبيعي الجاف.",
                'price' => 429,
                'compare_at_price' => null,
                'low_stock_threshold' => 4,
                'is_featured' => true,
                'is_new_arrival' => false,
                'specifications' => [
                    'الخامة' => 'طبقة صناعية مقاومة للاحتكاك',
                    'النعل' => 'مسامير مخروطية FG',
                    'الأرضية' => 'العشب الطبيعي الجاف',
                    'العناية' => 'إزالة العشب والطين بعد الاستخدام وتجفيف الحذاء بعيدًا عن الحرارة',
                ],
                'seo_title' => 'Velocity FG | حذاء كرة قدم من SQUAD',
                'seo_description' => 'حذاء كرة قدم Velocity FG بمسامير ثبات للعشب الجاف. مقاسات 39 إلى 44 وطلب مباشر عبر واتساب.',
                'images' => ['product-boot.webp', 'product-boot-detail.webp'],
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'colors' => [
                    'BK' => ['name' => 'أسود', 'hex' => '#131313'],
                ],
                'stock' => ['BK' => [4, 6, 8, 5, 0, 3]],
                'age' => 9,
            ],
            [
                'category' => 'tshirts',
                'name' => 'Driflex Training Tee',
                'slug' => 'driflex-training-tee',
                'sku' => 'SQ-JS-001',
                'short_description' => 'تيشيرت تدريب بقصة مريحة وخامة خفيفة سريعة الجفاف.',
                'description' => "تيشيرت Driflex هو القطعة الأساسية في حقيبة التمرين. قماش خفيف يسحب الرطوبة عن الجسم ويجف سريعًا، مع قصّة مستقيمة تسمح بحرية الحركة.\n\nحواف الأكمام والرقبة مشطبة لمظهر نظيف، والطباعة الخفيفة على الصدر لا تؤثر على تهوية القماش.\n\nيناسب التدريب في الصالة والجري الخفيف والاستخدام اليومي.",
                'price' => 129,
                'compare_at_price' => null,
                'low_stock_threshold' => 5,
                'is_featured' => false,
                'is_new_arrival' => true,
                'specifications' => [
                    'الخامة' => 'بوليستر خفيف سريع الجفاف',
                    'القصة' => 'مستقيمة مريحة',
                    'الاستخدام' => 'التدريب والاستخدام اليومي',
                    'العناية' => 'غسيل آلي على حرارة منخفضة',
                ],
                'seo_title' => 'Driflex Training Tee | تيشيرت تدريب من SQUAD',
                'seo_description' => 'تيشيرت تدريب Driflex بخامة خفيفة سريعة الجفاف. مقاسات S إلى XL وألوان متعددة.',
                'images' => ['product-tee.webp', 'product-tee-detail.webp'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [
                    'BK' => ['name' => 'أسود', 'hex' => '#121212'],
                    'GY' => ['name' => 'رمادي', 'hex' => '#8f918c'],
                ],
                'stock' => ['BK' => [8, 10, 9, 6], 'GY' => [4, 7, 5, 0]],
                'age' => 1,
            ],
            [
                'category' => 'team-jerseys',
                'name' => 'Legacy Home Jersey',
                'slug' => 'legacy-home-jersey',
                'sku' => 'SQ-JS-002',
                'short_description' => 'قميص فريق بقصة رياضية وخامة مريحة مستوحاة من أقمصة الملاعب.',
                'description' => "Legacy Home Jersey قميص بقصة رياضية مستوحاة من ملاعب كرة القدم. خامة ناعمة وخفيفة تسمح بالتهوية، مع ياقة مريحة وأكمام قصيرة.\n\nالألوان محايدة سهلة التنسيق مع الشورت والأحذية، والتفاصيل الخلفية بسيطة دون مبالغة.\n\nمناسب للمشاهدة والاستخدام اليومي ولتنسيق طقم رياضي كامل.",
                'price' => 219,
                'compare_at_price' => 259,
                'low_stock_threshold' => 4,
                'is_featured' => true,
                'is_new_arrival' => false,
                'specifications' => [
                    'الخامة' => 'بوليستر ناعم متهوي',
                    'القصة' => 'رياضية مستقيمة',
                    'الاستخدام' => 'المشاهدة والاستخدام اليومي',
                    'العناية' => 'غسيل على الوجه الداخلي مع ألوان مشابهة',
                ],
                'seo_title' => 'Legacy Home Jersey | قميص فريق من SQUAD',
                'seo_description' => 'قميص Legacy Home Jersey بقصة رياضية وخامة مريحة. اختر المقاس واللون واطلب عبر واتساب.',
                'images' => ['product-jersey.webp', 'product-jersey-detail.webp'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [
                    'WH' => ['name' => 'أبيض', 'hex' => '#f4f4ee'],
                    'BK' => ['name' => 'أسود', 'hex' => '#161616'],
                ],
                'stock' => ['WH' => [5, 6, 4, 3], 'BK' => [2, 0, 5, 4]],
                'age' => 12,
            ],
            [
                'category' => 'tracksuits',
                'name' => 'Motion Tracksuit',
                'slug' => 'motion-tracksuit',
                'sku' => 'SQ-TR-001',
                'short_description' => 'طقم تدريب من قطعتين بقصة مريحة وتفاصيل لونية هادئة.',
                'description' => "طقم Motion مصمم للانتقال بسهولة بين الإحماء والتمرين والمهام اليومية. جاكيت بسحّاب كامل وياقة مرتفعة، وبنطال بقصة مستقيمة وخصر مرن.\n\nالخامة خفيفة إلى متوسطة الوزن، بملمس ناعم من الداخل يمنح دفئًا معتدلًا دون ثقل.\n\nالطقم يباع كقطعتين معًا. راجع دليل المقاسات قبل الطلب لتحديد المقاس الأنسب.",
                'price' => 329,
                'compare_at_price' => 389,
                'low_stock_threshold' => 4,
                'is_featured' => true,
                'is_new_arrival' => false,
                'specifications' => [
                    'المحتوى' => 'جاكيت وبنطال',
                    'الخامة' => 'مزيج بوليستر ناعم',
                    'القصة' => 'مريحة مستقيمة',
                    'العناية' => 'غسيل آلي على حرارة منخفضة',
                ],
                'seo_title' => 'Motion Tracksuit | طقم تدريب من SQUAD',
                'seo_description' => 'طقم تدريب Motion من قطعتين، خامة مريحة وقصة عملية. اختر مقاسك وأتمم الطلب عبر واتساب.',
                'images' => ['product-tracksuit.webp', 'product-tracksuit-detail.webp'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [
                    'BK' => ['name' => 'أسود', 'hex' => '#111111'],
                ],
                'stock' => ['BK' => [4, 6, 5, 3]],
                'age' => 18,
            ],
            [
                'category' => 'shorts',
                'name' => 'Flex Training Shorts',
                'slug' => 'flex-training-shorts',
                'sku' => 'SQ-TR-002',
                'short_description' => 'شورت تدريب خفيف بخصر مرن مناسب للتمرين والجري.',
                'description' => "شورت Flex مصمم لحركة غير مقيدة. خامة خفيفة تجف بسرعة، وخصر مرن مع رباط سحب يمنحك ثباتًا مناسبًا أثناء التدريب.\n\nجيبان جانبيان بسيطان للأساسيات اليومية، وقصة فوق الركبة تمنح حرية خطوة أكبر.\n\nمناسب لتدريب القوة والجري والاستخدام اليومي.",
                'price' => 119,
                'compare_at_price' => null,
                'low_stock_threshold' => 5,
                'is_featured' => false,
                'is_new_arrival' => true,
                'specifications' => [
                    'الخامة' => 'بوليستر خفيف',
                    'الخصر' => 'مرن مع رباط سحب',
                    'القصة' => 'فوق الركبة',
                    'العناية' => 'غسيل آلي على حرارة منخفضة',
                ],
                'seo_title' => 'Flex Training Shorts | شورت تدريب من SQUAD',
                'seo_description' => 'شورت تدريب Flex خفيف بخصر مرن وجيوب جانبية. مقاسات وألوان متعددة والطلب عبر واتساب.',
                'images' => ['product-shorts.webp', 'product-shorts-detail.webp'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [
                    'BK' => ['name' => 'أسود', 'hex' => '#141414'],
                    'GY' => ['name' => 'رمادي', 'hex' => '#91938e'],
                ],
                'stock' => ['BK' => [7, 6, 5, 4], 'GY' => [3, 2, 4, 5]],
                'age' => 3,
            ],
            [
                'category' => 'bags',
                'name' => 'Duffel Pro 35L',
                'slug' => 'duffel-pro-35l',
                'sku' => 'SQ-AC-001',
                'short_description' => 'حقيبة رياضية بسعة 35 لترًا وخانة منفصلة للأحذية.',
                'description' => "Duffel Pro حقيبة عملية للصالة والملعب والتنقل اليومي. سعة 35 لترًا تكفي لملابس التمرين وحذاء وتفاصيلك الشخصية.\n\nخانة منفصلة للأحذية تحافظ على باقي الأغراض جافة ونظيفة، وحزام كتف قابل للتعديل مع مقبضين جانبيين.\n\nالحقيبة قابلة للطي نسبيًا عند عدم الاستخدام، وتُمسح بسهولة بقطعة قماش رطبة.",
                'price' => 189,
                'compare_at_price' => 229,
                'low_stock_threshold' => 3,
                'is_featured' => true,
                'is_new_arrival' => false,
                'specifications' => [
                    'السعة' => '35 لترًا',
                    'الخامة' => 'بوليستر مقاوم للماء الخفيف',
                    'الجيوب' => 'خانة أحذية منفصلة وجيب داخلي',
                    'العناية' => 'مسح بقطعة قماش رطبة',
                ],
                'seo_title' => 'Duffel Pro 35L | حقيبة رياضية من SQUAD',
                'seo_description' => 'حقيبة Duffel Pro الرياضية بسعة 35 لترًا وخانة أحذية منفصلة. اطلبها الآن عبر واتساب.',
                'images' => ['product-duffel.webp', 'product-duffel-detail.webp'],
                'stock_quantity' => 12,
                'age' => 22,
            ],
            [
                'category' => 'caps-bottles',
                'name' => 'Steel Bottle 750ml',
                'slug' => 'steel-bottle-750ml',
                'sku' => 'SQ-AC-002',
                'short_description' => 'زجاجة مياه ستانلس ستيل بسعة 750 مل وغطاء محكم.',
                'description' => "زجاجة Steel بسعة 750 مل مصنوعة من ستانلس ستيل بطبقة خارجية مطفية سهلة الإمساك. الغطاء محكم مع حلقة حمل صغيرة.\n\nملمس خارجي غير لزج، وفتحة واسعة تسهل التعبئة والتنظيف. مناسبة للصالة والتنقل اليومي.\n\nيُنصح بغسلها يدويًا وتجفيفها جيدًا قبل الاستخدام الأول.",
                'price' => 69,
                'compare_at_price' => null,
                'low_stock_threshold' => 5,
                'is_featured' => false,
                'is_new_arrival' => true,
                'specifications' => [
                    'السعة' => '750 مل',
                    'الخامة' => 'ستانلس ستيل',
                    'الغطاء' => 'غطاء لولبي محكم مع حلقة حمل',
                    'العناية' => 'غسيل يدوي',
                ],
                'seo_title' => 'Steel Bottle 750ml | زجاجة مياه من SQUAD',
                'seo_description' => 'زجاجة مياه Steel من ستانلس ستيل بسعة 750 مل وغطاء محكم. اطلبها عبر واتساب.',
                'images' => ['product-bottle.webp', 'product-bottle-detail.webp'],
                'stock_quantity' => 4,
                'age' => 4,
            ],
            [
                'category' => 'caps-bottles',
                'name' => 'Air Performance Cap',
                'slug' => 'air-performance-cap',
                'sku' => 'SQ-AC-003',
                'short_description' => 'كاب رياضي خفيف بتصميم متهوي وحزام خلفي قابل للتعديل.',
                'description' => "كاب Air خفيف مناسب للجري والتمارين الخارجية. الجزء الأمامي مبطن بلطف، والجوانب شبكية تسمح بمرور الهواء.\n\nالحزام الخلفي قابل للتعديل، والخصر الأمامي غير مقوّس بالكامل ليظل مريحًا لفترات طويلة.\n\nلون محايد يناسب أغلب الإطلالات الرياضية.",
                'price' => 79,
                'compare_at_price' => null,
                'low_stock_threshold' => 5,
                'is_featured' => false,
                'is_new_arrival' => true,
                'specifications' => [
                    'الخامة' => 'بوليستر متهوي',
                    'المقاس' => 'مقاس واحد قابل للتعديل',
                    'التصميم' => 'جوانب شبكية وحزام خلفي',
                    'العناية' => 'غسيل يدوي وتجفيف في الهواء',
                ],
                'seo_title' => 'Air Performance Cap | كاب رياضي من SQUAD',
                'seo_description' => 'كاب Air الرياضي الخفيف بجوانب شبكية وحزام قابل للتعديل. اطلبه عبر واتساب.',
                'images' => ['product-cap.webp', 'product-cap-detail.webp'],
                'stock_quantity' => 18,
                'age' => 5,
            ],
        ];

        foreach ($products as $data) {
            $this->seedProduct($data, $categories);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  Collection<string, int>  $categories
     */
    private function seedProduct(array $data, $categories): void
    {
        $product = Product::query()->firstOrCreate(
            ['slug' => $data['slug']],
            [
                'category_id' => $categories[$data['category']] ?? null,
                'name' => $data['name'],
                'sku' => $data['sku'],
                'brand' => 'SQUAD',
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'price' => $data['price'],
                'compare_at_price' => $data['compare_at_price'],
                'stock_quantity' => $data['stock_quantity'] ?? null,
                'low_stock_threshold' => $data['low_stock_threshold'],
                'is_featured' => $data['is_featured'],
                'is_new_arrival' => $data['is_new_arrival'],
                'is_active' => true,
                'specifications' => $data['specifications'],
                'seo_title' => $data['seo_title'],
                'seo_description' => $data['seo_description'],
            ],
        );

        if (! $product->wasRecentlyCreated) {
            return;
        }

        $product->forceFill(['created_at' => now()->subDays($data['age'])])->saveQuietly();

        foreach ($data['images'] as $index => $image) {
            $product->images()->create([
                'path' => $this->importSeedImage($image, 'products/'.$product->slug),
                'alt_text' => $data['name'].($index === 0 ? ' - الصورة الرئيسية' : ' - صورة تفصيلية'),
                'sort_order' => $index,
            ]);
        }

        if (! isset($data['sizes'], $data['colors'], $data['stock'])) {
            return;
        }

        foreach ($data['colors'] as $colorCode => $color) {
            foreach ($data['sizes'] as $index => $size) {
                $product->variants()->create([
                    'sku' => $product->sku.'-'.$size.'-'.$colorCode,
                    'size' => $size,
                    'color' => $color['name'],
                    'color_hex' => $color['hex'],
                    'stock_quantity' => $data['stock'][$colorCode][$index] ?? 0,
                    'is_active' => true,
                ]);
            }
        }
    }
}
