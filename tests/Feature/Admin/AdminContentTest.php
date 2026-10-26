<?php

use App\Models\Banner;
use App\Models\Faq;
use App\Models\Page;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
    StoreSetting::set('currency_symbol', 'ر.س');
});

function adminPagePayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'صفحة اختبار',
        'slug' => '',
        'content' => '<p>محتوى الصفحة.</p>',
        'meta_title' => null,
        'meta_description' => null,
        'is_active' => '1',
        'show_in_footer' => '1',
        'sort_order' => 3,
    ], $overrides);
}

function adminBannerPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'بانر اختبار',
        'subtitle' => 'وصف البانر.',
        'link_url' => '/products',
        'button_label' => 'تسوق الآن',
        'placement' => 'home_promo',
        'is_active' => '1',
        'sort_order' => 1,
    ], $overrides);
}

test('page content is sanitized before it is stored', function () {
    $this->actingAs($this->admin)->post(route('admin.pages.store'), adminPagePayload([
        'title' => 'صفحة محمية',
        'content' => '<p>نص آمن</p><script>alert("xss")</script><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">رابط</a>',
    ]))->assertSessionHasNoErrors();

    $page = Page::query()->firstOrFail();

    expect($page->content)
        ->toContain('نص آمن')
        ->not->toContain('<script')
        ->not->toContain('onerror')
        ->not->toContain('javascript:');
});

test('the about-us page keeps its slug and cannot be deleted', function () {
    $page = Page::factory()->create(['slug' => 'about-us', 'title' => 'من نحن']);

    $this->actingAs($this->admin)->put(
        route('admin.pages.update', $page),
        adminPagePayload(['title' => 'من نحن', 'slug' => 'new-about-slug']),
    )->assertSessionHasNoErrors();

    expect($page->refresh()->slug)->toBe('about-us');

    $this->actingAs($this->admin)->delete(route('admin.pages.destroy', $page))
        ->assertSessionHas('error');

    $this->assertModelExists($page);
});

test('pages can be created and deleted', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.pages.store'), adminPagePayload(['title' => 'سياسة الاستبدال']))
        ->assertRedirect();

    $page = Page::query()->firstOrFail();

    expect($page->slug)->not->toBe('');

    $this->actingAs($this->admin)
        ->delete(route('admin.pages.destroy', $page))
        ->assertRedirect(route('admin.pages.index'));

    $this->assertModelMissing($page);
});

test('banners require a safe link and an image on creation', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.banners.store'), adminBannerPayload([
        'link_url' => 'javascript:alert(1)',
    ]))->assertSessionHasErrors(['image', 'link_url']);

    $this->actingAs($this->admin)->post(route('admin.banners.store'), adminBannerPayload([
        'link_url' => '//evil.example',
        'image' => UploadedFile::fake()->image('banner.jpg', 1200, 600),
    ]))->assertSessionHasErrors('link_url');

    $this->assertDatabaseCount('banners', 0);
});

test('banners can be created with a safe external link and optimized image', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.banners.store'), adminBannerPayload([
        'link_url' => 'https://example.com/landing',
        'image' => UploadedFile::fake()->image('banner.jpg', 1200, 600),
    ]))->assertSessionHasNoErrors();

    $banner = Banner::query()->firstOrFail();

    expect($banner->image_path)->toEndWith('.webp')
        ->and($banner->link_url)->toBe('https://example.com/landing');

    Storage::disk('public')->assertExists($banner->image_path);
});

test('FAQs can be created updated and deleted', function () {
    $this->actingAs($this->admin)->post(route('admin.faqs.store'), [
        'question' => 'كم تستغرق مدة التوصيل؟',
        'answer' => 'يتم تأكيد مدة التوصيل عبر واتساب حسب المدينة.',
        'sort_order' => 1,
        'is_active' => '1',
    ])->assertRedirect(route('admin.faqs.index'));

    $faq = Faq::query()->firstOrFail();

    $this->actingAs($this->admin)->put(route('admin.faqs.update', $faq), [
        'question' => 'هل التوصيل متاح لكل المدن؟',
        'answer' => 'نؤكد التغطية عند التواصل عبر واتساب.',
        'sort_order' => 2,
        'is_active' => '0',
    ])->assertSessionHasNoErrors();

    expect($faq->refresh()->question)->toBe('هل التوصيل متاح لكل المدن؟')
        ->and($faq->is_active)->toBeFalse();

    $this->actingAs($this->admin)->delete(route('admin.faqs.destroy', $faq))
        ->assertRedirect(route('admin.faqs.index'));

    $this->assertModelMissing($faq);
});

test('product managers cannot manage content pages', function () {
    $productManager = User::factory()->create();
    $page = Page::factory()->create();

    $this->actingAs($productManager)
        ->get(route('admin.pages.index'))
        ->assertForbidden();

    $this->actingAs($productManager)
        ->delete(route('admin.pages.destroy', $page))
        ->assertForbidden();
});
