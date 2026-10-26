<?php

use App\Models\Category;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
    $this->category = Category::factory()->create();
});

function adminProductPayload(Category $category, array $overrides = []): array
{
    return array_merge([
        'category_id' => $category->id,
        'name' => 'منتج اختبار',
        'slug' => '',
        'sku' => 'TEST-'.strtoupper(Str::random(8)),
        'brand' => 'SQUAD',
        'short_description' => 'وصف قصير للمنتج.',
        'description' => 'وصف كامل للمنتج.',
        'price' => '199.00',
        'compare_at_price' => null,
        'stock_quantity' => 10,
        'low_stock_threshold' => 5,
        'is_featured' => '1',
        'is_new_arrival' => '0',
        'is_active' => '1',
        'seo_title' => null,
        'seo_description' => null,
    ], $overrides);
}

test('an admin can create a product with a generated slug', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'name' => 'حذاء الجري السريع',
    ]));

    $product = Product::query()->firstOrFail();

    $response->assertRedirect(route('admin.products.edit', $product));

    expect($product->slug)->not->toBe('')
        ->and($product->is_active)->toBeTrue()
        ->and($product->is_featured)->toBeTrue()
        ->and($product->stock_quantity)->toBe(10);
});

test('product creation validates required fields and unique sku', function () {
    Product::factory()->create(['sku' => 'TAKEN-SKU']);

    $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'name' => '',
        'sku' => 'TAKEN-SKU',
        'price' => 'not-a-price',
    ]))->assertSessionHasErrors(['name', 'sku', 'price']);
});

test('product creation stores optimized responsive images', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('shoe.jpg', 1000, 1000);

    $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'images' => [$file],
    ]))->assertSessionHasNoErrors();

    $product = Product::query()->firstOrFail();
    $image = $product->images()->firstOrFail();

    expect($image->path)->toEndWith('.webp');

    $files = Storage::disk('public')->allFiles('products');

    expect($files)->toHaveCount(3)
        ->and(collect($files)->every(fn (string $path) => str_ends_with($path, '.webp')))->toBeTrue();
});

test('images must meet minimum dimensions', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'images' => [UploadedFile::fake()->image('tiny.jpg', 100, 100)],
    ]))->assertSessionHasErrors('images.0');

    $this->assertDatabaseCount('products', 0);
});

test('updating a simple product records a stock adjustment', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => 5,
        'low_stock_threshold' => 2,
    ]);

    $this->actingAs($this->admin)->put(
        route('admin.products.update', $product),
        adminProductPayload($this->category, [
            'name' => $product->name,
            'sku' => $product->sku,
            'stock_quantity' => 9,
        ]),
    )->assertSessionHasNoErrors();

    expect($product->refresh()->stock_quantity)->toBe(9);

    $adjustment = InventoryAdjustment::query()->latest('id')->firstOrFail();

    expect($adjustment->quantity_before)->toBe(5)
        ->and($adjustment->quantity_change)->toBe(4)
        ->and($adjustment->quantity_after)->toBe(9)
        ->and($adjustment->reason)->toBe('correction')
        ->and($adjustment->user_id)->toBe($this->admin->id);
});

test('variant stock cannot be changed from the product edit form', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => null,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock_quantity' => 4,
    ]);

    $this->actingAs($this->admin)->put(
        route('admin.products.update', $product),
        adminProductPayload($this->category, [
            'name' => $product->name,
            'sku' => $product->sku,
            'stock_quantity' => 500,
        ]),
    )->assertSessionHasNoErrors();

    expect($variant->refresh()->stock_quantity)->toBe(4)
        ->and($product->refresh()->stock_quantity)->toBeNull();

    $this->assertDatabaseCount('inventory_adjustments', 0);
});

test('a product with inventory history cannot be deleted', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 10]);

    app(InventoryService::class)->adjust($product, null, 'add', 1, 'stock_received', null, $this->admin);

    $this->actingAs($this->admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertSessionHas('error');

    $this->assertModelExists($product);
});

test('deleting a product removes its image files', function () {
    Storage::fake('public');
    Storage::disk('public')->put('products/example.webp', 'image');
    Storage::disk('public')->put('products/example-480.webp', 'image');

    $product = Product::factory()->create(['category_id' => $this->category->id]);
    ProductImage::factory()->create(['product_id' => $product->id, 'path' => 'products/example.webp']);

    $this->actingAs($this->admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    $this->assertModelMissing($product);
    Storage::disk('public')->assertMissing('products/example.webp');
    Storage::disk('public')->assertMissing('products/example-480.webp');
});

test('variants can be added with an initial stock and updated with rules', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => null,
    ]);

    $this->actingAs($this->admin)->post(route('admin.products.variants.store', $product), [
        'sku' => 'VAR-L-BLACK',
        'size' => 'L',
        'color' => 'أسود',
        'color_hex' => '#111111',
        'price_override' => '249.00',
        'stock_quantity' => 6,
        'is_active' => '1',
    ])->assertSessionHasNoErrors();

    $variant = ProductVariant::query()->firstOrFail();

    expect($variant->stock_quantity)->toBe(6)
        ->and($variant->price_override)->toBe('249.00');

    $this->actingAs($this->admin)->put(
        route('admin.products.variants.update', [$product, $variant]),
        [
            'sku' => 'VAR-L-BLACK',
            'size' => 'XL',
            'color' => 'أسود',
            'color_hex' => '#111111',
            'price_override' => '259.00',
            'stock_quantity' => 3,
            'is_active' => '1',
        ],
    )->assertSessionHasErrors('stock_quantity');

    expect($variant->refresh()->size)->toBe('L');

    $this->actingAs($this->admin)->put(
        route('admin.products.variants.update', [$product, $variant]),
        [
            'sku' => 'VAR-L-BLACK',
            'size' => 'XL',
            'color' => 'أسود',
            'color_hex' => '#111111',
            'price_override' => '259.00',
            'is_active' => '1',
        ],
    )->assertSessionHasNoErrors();

    expect($variant->refresh()->size)->toBe('XL')
        ->and($variant->stock_quantity)->toBe(6);
});

test('nested variant routes reject variants from another product', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $other = Product::factory()->create(['category_id' => $this->category->id]);
    $foreign = ProductVariant::factory()->create(['product_id' => $other->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.products.variants.destroy', [$product, $foreign]))
        ->assertNotFound();

    $this->assertModelExists($foreign);
});

function adminMinimalGlb(): string
{
    $json = json_encode([
        'asset' => ['version' => '2.0'],
        'meshes' => [['primitives' => []]],
    ]);
    $json = str_pad($json, (int) (ceil(strlen($json) / 4) * 4), ' ');
    $chunkLength = strlen($json);
    $totalLength = 12 + 8 + $chunkLength;

    return 'glTF'.pack('V', 2).pack('V', $totalLength).pack('V', $chunkLength).'JSON'.$json;
}

test('a valid GLB model is validated and stored with the product', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'model_3d' => UploadedFile::fake()->createWithContent('model.glb', adminMinimalGlb()),
    ]))->assertSessionHasNoErrors();

    $product = Product::query()->firstOrFail();

    expect($product->model_3d_path)->not->toBeNull()->toEndWith('.glb');
    Storage::disk('public')->assertExists($product->model_3d_path);
});

test('a model file with invalid content is rejected', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.products.store'), adminProductPayload($this->category, [
        'model_3d' => UploadedFile::fake()->createWithContent('fake.glb', 'not-a-real-model'),
    ]))->assertSessionHasErrors('model_3d');

    $this->assertDatabaseCount('products', 0);
    expect(Storage::disk('public')->allFiles('models'))->toBe([]);
});

test('regular staff cannot manage products', function () {
    $inventoryManager = User::factory()->inventoryManager()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->actingAs($inventoryManager)
        ->post(route('admin.products.store'), adminProductPayload($this->category))
        ->assertForbidden();

    $this->actingAs($inventoryManager)
        ->delete(route('admin.products.destroy', $product))
        ->assertForbidden();
});
