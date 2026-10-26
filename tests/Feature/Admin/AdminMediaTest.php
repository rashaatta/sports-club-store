<?php

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
    Storage::fake('public');
});

test('media uploads are optimized to webp responsive variants', function () {
    $this->actingAs($this->admin)->post(route('admin.media.store'), [
        'image' => UploadedFile::fake()->image('photo.jpg', 1200, 1200),
    ])->assertSessionHasNoErrors();

    $files = Storage::disk('public')->allFiles('media');

    expect($files)->toHaveCount(3)
        ->and(collect($files)->every(fn (string $path) => str_ends_with($path, '.webp')))->toBeTrue();
});

test('an unused media file can be deleted', function () {
    $path = 'media/example.webp';
    Storage::disk('public')->put($path, 'image');

    $this->actingAs($this->admin)
        ->delete(route('admin.media.destroy'), ['path' => $path])
        ->assertSessionHas('status');

    Storage::disk('public')->assertMissing($path);
});

test('media files referenced by content cannot be deleted', function () {
    $path = 'media/in-use.webp';
    Storage::disk('public')->put($path, 'image');

    $product = Product::factory()->create();
    ProductImage::factory()->create(['product_id' => $product->id, 'path' => $path]);

    $this->actingAs($this->admin)
        ->delete(route('admin.media.destroy'), ['path' => $path])
        ->assertSessionHas('error');

    Storage::disk('public')->assertExists($path);
});

test('path traversal attempts are rejected', function () {
    Storage::disk('public')->put('media/safe.webp', 'image');

    $this->actingAs($this->admin)
        ->delete(route('admin.media.destroy'), ['path' => 'media/../.env'])
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->delete(route('admin.media.destroy'), ['path' => 'secret/file.webp'])
        ->assertNotFound();
});

test('media permissions follow product and content permissions', function () {
    $inventoryManager = User::factory()->inventoryManager()->create();
    $contentManager = User::factory()->contentManager()->create();

    $this->actingAs($inventoryManager)->get(route('admin.media.index'))->assertForbidden();
    $this->actingAs($inventoryManager)->post(route('admin.media.store'), [
        'image' => UploadedFile::fake()->image('blocked.jpg', 800, 800),
    ])->assertForbidden();

    $this->actingAs($contentManager)->get(route('admin.media.index'))->assertOk();
});
