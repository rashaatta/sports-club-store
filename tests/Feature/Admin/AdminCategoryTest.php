<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

function adminCategoryPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'فئة اختبار',
        'slug' => '',
        'parent_id' => null,
        'description' => 'وصف الفئة.',
        'display_order' => 3,
        'is_featured' => '0',
        'is_active' => '1',
        'seo_title' => null,
        'seo_description' => null,
    ], $overrides);
}

test('an admin can create a category with a generated unique slug', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), adminCategoryPayload(['name' => 'أحذية رياضية']))
        ->assertRedirect(route('admin.categories.index'));

    $first = Category::query()->firstOrFail();

    expect($first->slug)->not->toBe('')
        ->and($first->slug)->not->toBe('item');

    $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), adminCategoryPayload(['name' => 'أحذية رياضية']))
        ->assertSessionHasNoErrors();

    $second = Category::query()->latest('id')->firstOrFail();

    expect($second->slug)->toBe($first->slug.'-2');
});

test('categories can be nested under root categories only', function () {
    $root = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $root->id]);

    $this->actingAs($this->admin)->put(
        route('admin.categories.update', $child),
        adminCategoryPayload(['name' => $child->name, 'parent_id' => $root->id]),
    )->assertSessionHasNoErrors();

    $this->actingAs($this->admin)->put(
        route('admin.categories.update', Category::factory()->create()),
        adminCategoryPayload(['name' => 'فئة جديدة', 'parent_id' => $child->id]),
    )->assertSessionHasErrors('parent_id');
});

test('a category with children cannot be moved under another category', function () {
    $root = Category::factory()->create();
    $otherRoot = Category::factory()->create();
    Category::factory()->create(['parent_id' => $root->id]);

    $this->actingAs($this->admin)->put(
        route('admin.categories.update', $root),
        adminCategoryPayload(['name' => $root->name, 'parent_id' => $otherRoot->id]),
    )->assertSessionHasErrors('parent_id');

    expect($root->refresh()->parent_id)->toBeNull();
});

test('categories with children or products cannot be deleted', function () {
    $root = Category::factory()->create();
    Category::factory()->create(['parent_id' => $root->id]);

    $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $root))
        ->assertSessionHas('error');
    $this->assertModelExists($root);

    $withoutChildren = Category::factory()->create();
    Product::factory()->create(['category_id' => $withoutChildren->id]);

    $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $withoutChildren))
        ->assertSessionHas('error');
    $this->assertModelExists($withoutChildren);

    $empty = Category::factory()->create();
    $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $empty))
        ->assertRedirect(route('admin.categories.index'));
    $this->assertModelMissing($empty);
});

test('category images are optimized and can be removed', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.categories.store'), adminCategoryPayload([
        'name' => 'فئة بصور',
        'image' => UploadedFile::fake()->image('category.jpg', 900, 900),
    ]))->assertSessionHasNoErrors();

    $category = Category::query()->firstOrFail();

    expect($category->image_path)->toEndWith('.webp');
    Storage::disk('public')->assertExists($category->image_path);

    $this->actingAs($this->admin)->put(route('admin.categories.update', $category), adminCategoryPayload([
        'name' => $category->name,
        'remove_image' => '1',
    ]))->assertSessionHasNoErrors();

    expect($category->refresh()->image_path)->toBeNull();
});

test('content managers cannot manage categories', function () {
    $contentManager = User::factory()->contentManager()->create();
    $category = Category::factory()->create();

    $this->actingAs($contentManager)
        ->post(route('admin.categories.store'), adminCategoryPayload())
        ->assertForbidden();

    $this->actingAs($contentManager)
        ->delete(route('admin.categories.destroy', $category))
        ->assertForbidden();
});
