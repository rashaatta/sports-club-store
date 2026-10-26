<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    StoreSetting::set('store_name', 'SQUAD');
});

test('the storefront home page renders active catalog content only', function () {
    $category = Category::factory()->create(['name' => 'أحذية']);
    $featured = Product::factory()->featured()->create([
        'category_id' => $category->id,
        'name' => 'حذاء ملعب مميز',
    ]);
    Product::factory()->inactive()->featured()->create([
        'category_id' => $category->id,
        'name' => 'حذاء مخفي',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('حذاء ملعب مميز')
        ->assertDontSee('حذاء مخفي')
        ->assertSee('dir="rtl"', false)
        ->assertSee($featured->url);
});

test('the product listing only shows active products', function () {
    Product::factory()->create(['name' => 'منتج ظاهر']);
    Product::factory()->inactive()->create(['name' => 'منتج غير نشط']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('منتج ظاهر')
        ->assertDontSee('منتج غير نشط');
});

test('products can be searched by name and sku', function () {
    Product::factory()->create(['name' => 'قميص مباراة', 'sku' => 'JERSEY-01']);
    Product::factory()->create(['name' => 'شورت تدريب', 'sku' => 'SHORT-99']);

    $this->get(route('products.index', ['q' => 'مباراة']))
        ->assertOk()
        ->assertSee('قميص مباراة')
        ->assertDontSee('شورت تدريب');

    $this->get(route('products.index', ['q' => 'SHORT-99']))
        ->assertOk()
        ->assertSee('شورت تدريب')
        ->assertDontSee('قميص مباراة');
});

test('category listings include descendant category products', function () {
    $parent = Category::factory()->create(['name' => 'أحذية']);
    $child = Category::factory()->create(['name' => 'أحذية جري', 'parent_id' => $parent->id]);
    $other = Category::factory()->create(['name' => 'إكسسوارات']);

    Product::factory()->create(['name' => 'حذاء ابن', 'category_id' => $child->id]);
    Product::factory()->create(['name' => 'منتج خارجي', 'category_id' => $other->id]);

    $this->get(route('categories.show', $parent))
        ->assertOk()
        ->assertSee('حذاء ابن')
        ->assertDontSee('منتج خارجي');

    $this->get(route('categories.show', Category::factory()->inactive()->create(['name' => 'مخفية'])))
        ->assertNotFound();
});

test('catalog filters apply price availability flags and variants', function () {
    $category = Category::factory()->create();
    $cheap = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'رخيص',
        'price' => 100,
        'compare_at_price' => 150,
        'is_featured' => true,
        'stock_quantity' => 5,
    ]);
    $expensive = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'غالي',
        'price' => 500,
        'stock_quantity' => 5,
    ]);
    $sized = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'بمقاسات',
        'price' => 200,
        'stock_quantity' => null,
    ]);
    ProductVariant::factory()->create([
        'product_id' => $sized->id,
        'size' => 'XL',
        'color' => 'أحمر',
        'stock_quantity' => 2,
    ]);
    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'نفد',
        'stock_quantity' => null,
    ]);

    $this->get(route('products.index', ['max_price' => 150]))
        ->assertSee('رخيص')
        ->assertDontSee('غالي');

    $this->get(route('products.index', ['sizes' => ['XL']]))
        ->assertSee('بمقاسات')
        ->assertDontSee('رخيص');

    $this->get(route('products.index', ['colors' => ['أحمر']]))
        ->assertSee('بمقاسات')
        ->assertDontSee('غالي');

    $this->get(route('products.index', ['featured' => 1]))
        ->assertSee('رخيص')
        ->assertDontSee('غالي');

    $this->get(route('products.index', ['on_sale' => 1]))
        ->assertSee('رخيص')
        ->assertDontSee('غالي');

    $this->get(route('products.index', ['availability' => 'in_stock']))
        ->assertSee('رخيص')
        ->assertDontSee('نفد');

    $this->get(route('products.index', ['availability' => 'out_of_stock']))
        ->assertSee('نفد')
        ->assertDontSee('رخيص');

    $this->get(route('products.index', ['sort' => 'price_asc']))
        ->assertSeeInOrder(['رخيص', 'بمقاسات', 'غالي']);
});

test('an unknown category filter returns an empty result set', function () {
    Product::factory()->create(['name' => 'منتج موجود']);

    $this->get(route('products.index', ['category' => 'does-not-exist']))
        ->assertOk()
        ->assertDontSee('منتج موجود');
});

test('filtered catalog pages are marked noindex while clean pages are indexable', function () {
    $product = Product::factory()->create(['name' => 'منتج']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertSee('<link rel="canonical" href="'.route('products.index').'">', false);

    $this->get(route('products.index', ['q' => 'منتج']))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});

test('the product page renders active products with pricing and structured data', function () {
    $product = Product::factory()->create([
        'name' => 'Aero Runner',
        'price' => 349,
        'compare_at_price' => 429,
        'seo_title' => 'Aero Runner | حذاء جري',
    ]);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'size' => '42',
        'color' => 'أسود',
        'stock_quantity' => 4,
        'price_override' => 399,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Aero Runner')
        ->assertSee('Aero Runner | حذاء جري')
        ->assertSee('"@type":"Product"', false)
        ->assertSee('<link rel="canonical" href="'.$product->url.'">', false);
});

test('inactive products are not publicly accessible', function () {
    $product = Product::factory()->inactive()->create();

    $this->get(route('products.show', $product))->assertNotFound();
});

test('category search pages are marked noindex', function () {
    $category = Category::factory()->create();

    $this->get(route('categories.show', $category))
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false);

    $this->get(route('categories.show', ['category' => $category->slug, 'q' => 'حذاء']))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});

test('the 3D viewer only renders when a supported model file exists', function () {
    Storage::fake('public');

    $product = Product::factory()->create([
        'name' => 'حذاء ثلاثي الأبعاد',
        'model_3d_path' => 'models/missing.glb',
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertDontSee(__('storefront.product.view_3d'), false);

    Storage::disk('public')->put('models/present.glb', 'glTF');

    $product->update(['model_3d_path' => 'models/present.glb']);

    $response = $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee(__('storefront.product.view_3d'), false);

    $payload = Str::between($response->getContent(), 'productModel(JSON.parse(', '))');
    $src = Str::between($payload, 'src', 'alt');

    expect($src)
        ->toContain('present.glb')
        ->not->toContain('http');
});
