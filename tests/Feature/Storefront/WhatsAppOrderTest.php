<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Support\Money;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    StoreSetting::set('store_name', 'SQUAD');
    StoreSetting::set('currency_symbol', 'ر.س');
    StoreSetting::set('whatsapp_number', '+966 50 123 4567');
});

function orderUrlFromResponse(TestResponse $response): string
{
    return (string) $response->json('url');
}

function messageFromOrderUrl(string $url): string
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

    return (string) ($query['text'] ?? '');
}

test('a simple product order builds an encoded official WhatsApp URL', function () {
    $product = Product::factory()->create([
        'name' => 'Aero Runner',
        'slug' => 'aero-runner',
        'sku' => 'SQ-001',
        'price' => 349,
        'stock_quantity' => 10,
    ]);

    $response = $this->postJson(route('products.order', $product), [
        'quantity' => 2,
        'price' => 1,
    ])->assertOk();

    $url = orderUrlFromResponse($response);
    $parts = parse_url($url);

    expect($parts['scheme'])->toBe('https')
        ->and($parts['host'])->toBe('wa.me')
        ->and($parts['path'])->toBe('/966501234567');

    $message = messageFromOrderUrl($url);

    expect($message)
        ->toContain(__('storefront.whatsapp.order_greeting'))
        ->toContain('Aero Runner')
        ->toContain('SQ-001')
        ->toContain(Money::format(349))
        ->toContain($product->url)
        ->toContain(__('storefront.whatsapp.quantity').': 2')
        ->not->toContain(Money::format(1));

    $this->assertDatabaseHas('analytics_events', [
        'event_name' => 'whatsapp_order_click',
        'product_id' => $product->id,
        'variant_id' => null,
        'source' => 'product_page',
    ]);
});

test('a variant order uses the server-side variant price and options', function () {
    $product = Product::factory()->create([
        'name' => 'Match Jersey',
        'price' => 200,
        'stock_quantity' => null,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'sku' => 'JERSEY-L-WHITE',
        'size' => 'L',
        'color' => 'أبيض',
        'color_hex' => '#ffffff',
        'price_override' => 249,
        'stock_quantity' => 5,
    ]);

    $response = $this->postJson(route('products.order', $product), [
        'variant_id' => $variant->id,
        'quantity' => 1,
    ])->assertOk();

    $message = messageFromOrderUrl(orderUrlFromResponse($response));

    expect($message)
        ->toContain('JERSEY-L-WHITE')
        ->toContain(Money::format(249))
        ->toContain(__('storefront.whatsapp.size').': L')
        ->toContain(__('storefront.whatsapp.color').': أبيض');

    $this->assertDatabaseHas('analytics_events', [
        'product_id' => $product->id,
        'variant_id' => $variant->id,
    ]);
});

test('a variant is required when the product uses variants', function () {
    $product = Product::factory()->create(['stock_quantity' => null]);
    ProductVariant::factory()->create(['product_id' => $product->id, 'stock_quantity' => 5]);

    $this->postJson(route('products.order', $product), ['quantity' => 1])
        ->assertStatus(422)
        ->assertJsonValidationErrors('variant_id');
});

test('client supplied variants must belong to the product and be active', function () {
    $variantProduct = Product::factory()->create(['stock_quantity' => null]);
    ProductVariant::factory()->create(['product_id' => $variantProduct->id]);

    $otherProduct = Product::factory()->create(['stock_quantity' => null]);
    $foreignVariant = ProductVariant::factory()->create(['product_id' => $otherProduct->id]);

    $this->postJson(route('products.order', $variantProduct), [
        'variant_id' => $foreignVariant->id,
        'quantity' => 1,
    ])->assertStatus(422)->assertJsonValidationErrors('variant_id');

    $inactive = ProductVariant::factory()->create([
        'product_id' => $variantProduct->id,
        'is_active' => false,
    ]);

    $this->postJson(route('products.order', $variantProduct), [
        'variant_id' => $inactive->id,
        'quantity' => 1,
    ])->assertStatus(422)->assertJsonValidationErrors('variant_id');
});

test('a simple product rejects a variant identifier', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);
    $foreignVariant = ProductVariant::factory()->create();

    $this->postJson(route('products.order', $product), [
        'variant_id' => $foreignVariant->id,
        'quantity' => 1,
    ])->assertStatus(422)->assertJsonValidationErrors('variant_id');
});

test('orders cannot exceed available stock', function () {
    $simple = Product::factory()->create(['stock_quantity' => 2]);
    $variantProduct = Product::factory()->create(['stock_quantity' => null]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $variantProduct->id,
        'stock_quantity' => 1,
    ]);

    $this->postJson(route('products.order', $simple), ['quantity' => 3])
        ->assertStatus(422)
        ->assertJsonValidationErrors('quantity');

    $this->postJson(route('products.order', $variantProduct), [
        'variant_id' => $variant->id,
        'quantity' => 2,
    ])->assertStatus(422)->assertJsonValidationErrors('quantity');
});

test('quantity is bounded by the configured maximum', function () {
    $product = Product::factory()->create(['stock_quantity' => 1000]);

    $this->postJson(route('products.order', $product), ['quantity' => 21])
        ->assertStatus(422)
        ->assertJsonValidationErrors('quantity');
});

test('ordering fails clearly when WhatsApp is not configured', function () {
    StoreSetting::set('whatsapp_number', '');
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $this->postJson(route('products.order', $product), ['quantity' => 1])
        ->assertStatus(422)
        ->assertJsonValidationErrors('whatsapp');

    $this->assertDatabaseCount('analytics_events', 0);
});

test('inactive products cannot be ordered', function () {
    $product = Product::factory()->inactive()->create(['stock_quantity' => 5]);

    $this->postJson(route('products.order', $product), ['quantity' => 1])
        ->assertNotFound();

    $this->assertDatabaseCount('analytics_events', 0);
});

test('non ajax requests are redirected to WhatsApp', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $response = $this->post(route('products.order', $product), ['quantity' => 1]);

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toStartWith('https://wa.me/966501234567?text=');
});
