<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\StoreSettingsSeeder;

beforeEach(function () {
    $this->seed(StoreSettingsSeeder::class);
});

test('the storefront defaults to Arabic RTL', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('كل المنتجات');
});

test('a guest can switch to English and is redirected back safely', function () {
    $this->get(route('locale.switch', ['locale' => 'en', 'redirect' => '/products?sort=price_asc']))
        ->assertRedirect('/products?sort=price_asc')
        ->assertSessionHas('locale', 'en')
        ->assertCookie('locale', 'en');
});

test('English renders LTR storefront copy', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false)
        ->assertSee('All products')
        ->assertSee('Offers');
});

test('English product pages use translated option labels', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Test Runner',
        'price' => 349,
    ]);
    ProductVariant::factory()->for($product)->create([
        'size' => '42',
        'color' => 'Black',
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Size')
        ->assertSee('Color')
        ->assertSee('Quantity')
        ->assertSee('SAR');
});

test('the admin dashboard renders in English for an authenticated user', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false)
        ->assertSee('Dashboard')
        ->assertSee('View store');
});

test('the admin profile shows translated role labels in English', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.profile.edit'))
        ->assertOk()
        ->assertSee('Super Admin');
});

test('a user can switch back to Arabic', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('locale.switch', ['locale' => 'ar', 'redirect' => '/']))
        ->assertRedirect('/')
        ->assertSessionHas('locale', 'ar')
        ->assertCookie('locale', 'ar');
});

test('an unknown locale returns a 404', function () {
    $this->get('/locale/fr')->assertNotFound();
});

test('external redirect targets are discarded', function (string $target) {
    $this->get(route('locale.switch', ['locale' => 'en', 'redirect' => $target]))
        ->assertRedirect(route('home'));
})->with([
    'absolute url' => 'https://evil.test/phishing',
    'protocol relative url' => '//evil.test/phishing',
    'backslash path' => '/\\evil.test/phishing',
]);

test('the switch route sends authenticated users to the dashboard by default', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('locale.switch', ['locale' => 'en']))
        ->assertRedirect(route('admin.dashboard'));
});

test('the language switcher exposes the other locale', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('English');

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertSee('العربية');
});

test('the WhatsApp order message follows the selected locale', function () {
    $product = Product::factory()->create([
        'name' => 'Aero Runner',
        'sku' => 'SQ-001',
        'price' => 349,
        'stock_quantity' => 5,
    ]);

    $response = $this->withSession(['locale' => 'en'])
        ->postJson(route('products.order', $product), ['quantity' => 1])
        ->assertOk();

    parse_str((string) parse_url((string) $response->json('url'), PHP_URL_QUERY), $query);
    $message = (string) ($query['text'] ?? '');

    expect($message)
        ->toContain('Hello, I would like to ask about and order the following product:')
        ->toContain('Product name: Aero Runner')
        ->toContain('Listed price: 349.00 SAR')
        ->toContain('Quantity: 1')
        ->not->toContain('السلام عليكم');
});
