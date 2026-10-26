<?php

use App\Models\Category;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
    $this->category = Category::factory()->create();
});

function inventoryPayload(array $overrides = []): array
{
    return array_merge([
        'mode' => 'add',
        'quantity' => 5,
        'reason' => 'stock_received',
        'note' => 'اختبار',
    ], $overrides);
}

test('inventory can be added removed and set with an audit trail', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => 10,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload())
        ->assertSessionHasNoErrors();
    expect($product->refresh()->stock_quantity)->toBe(15);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload(['mode' => 'remove', 'quantity' => 5]))
        ->assertSessionHasNoErrors();
    expect($product->refresh()->stock_quantity)->toBe(10);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload(['mode' => 'set', 'quantity' => 3]))
        ->assertSessionHasNoErrors();
    expect($product->refresh()->stock_quantity)->toBe(3);

    $adjustments = InventoryAdjustment::query()->orderBy('id')->get();

    expect($adjustments)->toHaveCount(3)
        ->and($adjustments[0]->quantity_before)->toBe(10)
        ->and($adjustments[0]->quantity_change)->toBe(5)
        ->and($adjustments[0]->quantity_after)->toBe(15)
        ->and($adjustments[1]->quantity_change)->toBe(-5)
        ->and($adjustments[2]->quantity_after)->toBe(3)
        ->and($adjustments->every(fn ($row) => $row->user_id === $this->admin->id))->toBeTrue();
});

test('inventory cannot be removed below zero', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => 2,
    ]);

    $this->actingAs($this->admin)
        ->from(route('admin.inventory.show', $product))
        ->post(route('admin.inventory.store', $product), inventoryPayload(['mode' => 'remove', 'quantity' => 5]))
        ->assertSessionHasErrors('quantity');

    expect($product->refresh()->stock_quantity)->toBe(2);
    $this->assertDatabaseCount('inventory_adjustments', 0);
});

test('variant products require a variant and update only that variant', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => null,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock_quantity' => 2,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload())
        ->assertSessionHasErrors('variant_id');

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload([
            'variant_id' => $variant->id,
            'quantity' => 4,
        ]))
        ->assertSessionHasNoErrors();

    expect($variant->refresh()->stock_quantity)->toBe(6);

    $this->assertDatabaseHas('inventory_adjustments', [
        'product_id' => $product->id,
        'variant_id' => $variant->id,
        'quantity_after' => 6,
    ]);
});

test('a variant belonging to another product cannot be adjusted', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => null]);
    ProductVariant::factory()->create(['product_id' => $product->id]);

    $other = Product::factory()->create(['category_id' => $this->category->id]);
    $foreign = ProductVariant::factory()->create(['product_id' => $other->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload([
            'variant_id' => $foreign->id,
        ]))
        ->assertNotFound();
});

test('no-op adjustments do not create history rows', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock_quantity' => 5,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.inventory.store', $product), inventoryPayload(['mode' => 'set', 'quantity' => 5]))
        ->assertSessionHas('status', __('admin.inventory.no_change'));

    $this->assertDatabaseCount('inventory_adjustments', 0);
});

test('inventory managers can access inventory but not products administration', function () {
    $inventoryManager = User::factory()->inventoryManager()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->actingAs($inventoryManager)
        ->get(route('admin.inventory.index'))
        ->assertOk();

    $this->actingAs($inventoryManager)
        ->post(route('admin.inventory.store', $product), inventoryPayload())
        ->assertSessionHasNoErrors();

    $this->actingAs($inventoryManager)
        ->get(route('admin.products.edit', $product))
        ->assertForbidden();
});

test('content managers cannot adjust inventory', function () {
    $contentManager = User::factory()->contentManager()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->actingAs($contentManager)
        ->get(route('admin.inventory.index'))
        ->assertForbidden();

    $this->actingAs($contentManager)
        ->post(route('admin.inventory.store', $product), inventoryPayload())
        ->assertForbidden();
});
