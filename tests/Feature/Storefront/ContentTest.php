<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\StoreSetting;

beforeEach(function () {
    StoreSetting::set('store_name', 'SQUAD');
    StoreSetting::set('whatsapp_number', '966501234567');
});

test('the about page requires an active about-us page', function () {
    $this->get(route('about'))->assertNotFound();

    $page = Page::factory()->create([
        'slug' => 'about-us',
        'title' => 'من نحن',
        'content' => '<p>قصة المتجر.</p>',
        'is_active' => true,
        'show_in_footer' => false,
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('من نحن')
        ->assertSee('قصة المتجر.')
        ->assertSee('<link rel="canonical" href="'.route('about').'">', false);

    $page->update(['is_active' => false]);
    $this->get(route('about'))->assertNotFound();
});

test('policy pages render and the about-us alias redirects permanently', function () {
    $policy = Page::factory()->create([
        'slug' => 'privacy-policy',
        'title' => 'سياسة الخصوصية',
        'content' => '<p>نص السياسة.</p>',
    ]);

    $this->get(route('pages.show', $policy))
        ->assertOk()
        ->assertSee('سياسة الخصوصية')
        ->assertSee('نص السياسة.');

    Page::factory()->create(['slug' => 'about-us', 'title' => 'من نحن', 'content' => '<p>قصتنا.</p>']);

    $this->get(route('pages.show', 'about-us'))
        ->assertStatus(301)
        ->assertRedirect(route('about'));
});

test('the FAQ page lists active entries in order', function () {
    Faq::factory()->create(['question' => 'سؤال نشط أول', 'sort_order' => 1]);
    Faq::factory()->create(['question' => 'سؤال نشط ثانٍ', 'sort_order' => 2]);
    Faq::factory()->create(['question' => 'سؤال مخفي', 'is_active' => false]);

    $this->get(route('faq'))
        ->assertOk()
        ->assertSeeInOrder(['سؤال نشط أول', 'سؤال نشط ثانٍ'])
        ->assertDontSee('سؤال مخفي');
});

test('the sitemap contains only public active content', function () {
    $activeCategory = Category::factory()->create(['slug' => 'active-category']);
    $inactiveCategory = Category::factory()->inactive()->create(['slug' => 'inactive-category']);
    $activeProduct = Product::factory()->create(['slug' => 'active-product', 'category_id' => $activeCategory->id]);
    $inactiveProduct = Product::factory()->inactive()->create(['slug' => 'inactive-product']);
    $activePage = Page::factory()->create(['slug' => 'active-page']);
    $inactivePage = Page::factory()->create(['slug' => 'inactive-page', 'is_active' => false]);
    Page::factory()->create(['slug' => 'about-us']);

    $response = $this->get(route('sitemap'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('products.show', $activeProduct), false)
        ->assertSee(route('categories.show', $activeCategory), false)
        ->assertSee(route('pages.show', $activePage), false)
        ->assertDontSee(route('products.show', $inactiveProduct), false)
        ->assertDontSee(route('categories.show', $inactiveCategory), false)
        ->assertDontSee(route('pages.show', $inactivePage), false)
        ->assertDontSee('/admin', false);
});

test('robots disallows private areas and references the sitemap', function () {
    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Disallow: /login')
        ->assertSee('Sitemap: '.route('sitemap'))
        ->assertDontSee('/profile');
});

test('the contact form stores a valid message', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('wa.me/966501234567', false);

    $payload = [
        'name' => 'عميل تجريبي',
        'email' => 'customer@example.test',
        'phone' => '+966500000000',
        'message' => 'أرغب في الاستفسار عن توفر المقاس 42.',
    ];

    $this->from(route('contact'))->post(route('contact.store'), $payload)
        ->assertRedirect(route('contact'))
        ->assertSessionHas('status', __('storefront.contact.sent'));

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'عميل تجريبي',
        'email' => 'customer@example.test',
        'phone' => '+966500000000',
        'read_at' => null,
    ]);
});

test('the contact form validates required fields', function () {
    $this->from(route('contact'))->post(route('contact.store'), [
        'name' => 'ع',
        'email' => 'not-an-email',
        'message' => 'قصير',
    ])->assertRedirect(route('contact'))
        ->assertSessionHasErrors(['name', 'email', 'message']);

    expect(ContactMessage::query()->count())->toBe(0);
});

test('the contact honeypot silently discards bots', function () {
    $this->post(route('contact.store'), [
        'name' => 'بوت',
        'email' => 'bot@example.test',
        'message' => 'رسالة من بوت تلقائي.',
        'website' => 'https://spam.example',
    ])->assertSessionHas('status');

    expect(ContactMessage::query()->count())->toBe(0);
});

test('contact submissions are rate limited', function () {
    $payload = [
        'name' => 'عميل تجريبي',
        'email' => 'rate@example.test',
        'message' => 'رسالة اختبار الحد الأقصى للطلبات.',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('contact.store'), $payload)->assertRedirect();
    }

    $this->post(route('contact.store'), $payload)->assertStatus(429);
});
