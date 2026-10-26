<?php

use App\Enums\Role;
use App\Models\Banner;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\StoreSettingsSeeder;

beforeEach(function () {
    $this->seed(StoreSettingsSeeder::class);
    $this->admin = User::factory()->superAdmin()->create();
});

test('every admin screen renders for a super admin', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    ProductVariant::factory()->for($product)->create();
    $page = Page::factory()->create(['slug' => 'shipping-policy']);
    $banner = Banner::factory()->create();
    $faq = Faq::factory()->create();
    $member = User::factory()->create();
    $message = ContactMessage::query()->create([
        'name' => 'عميل تجريبي',
        'email' => 'customer@example.test',
        'phone' => '966500000000',
        'message' => 'هل يتوفر هذا المقاس؟',
    ]);

    $urls = [
        route('admin.dashboard'),
        route('admin.analytics.index'),
        route('admin.profile.edit'),
        route('admin.products.index'),
        route('admin.products.create'),
        route('admin.products.edit', $product),
        route('admin.categories.index'),
        route('admin.categories.create'),
        route('admin.categories.edit', $category),
        route('admin.inventory.index'),
        route('admin.inventory.show', $product),
        route('admin.pages.index'),
        route('admin.pages.create'),
        route('admin.pages.edit', $page),
        route('admin.banners.index'),
        route('admin.banners.create'),
        route('admin.banners.edit', $banner),
        route('admin.faqs.index'),
        route('admin.faqs.create'),
        route('admin.faqs.edit', $faq),
        route('admin.messages.index'),
        route('admin.messages.show', $message),
        route('admin.users.index'),
        route('admin.users.create'),
        route('admin.users.edit', $member),
        route('admin.settings.edit'),
        route('admin.media.index'),
    ];

    foreach ($urls as $url) {
        $this->actingAs($this->admin)
            ->get($url)
            ->assertOk();
    }
});

test('guests are redirected from the admin panel', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
});

test('role permissions gate each admin area', function (string $role, string $allowed, string $blocked) {
    $user = User::factory()->create(['role' => Role::from($role)]);

    $this->actingAs($user)->get($allowed)->assertOk();
    $this->actingAs($user)->get($blocked)->assertForbidden();
})->with([
    'inventory manager' => ['inventory_manager', '/admin/inventory', '/admin/products'],
    'content manager' => ['content_manager', '/admin/pages', '/admin/categories'],
]);
