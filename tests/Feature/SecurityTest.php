<?php

use App\Models\User;

test('security headers are added to public responses', function () {
    $this->get('/')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
});

test('hsts is only sent over secure requests', function () {
    $this->get('/')->assertHeaderMissing('Strict-Transport-Security');

    $this->get('https://localhost/')
        ->assertOk()
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

test('admin screens are marked noindex', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
});

test('missing pages render the branded Arabic error page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertSee('الصفحة غير موجودة')
        ->assertSee('dir="rtl"', false)
        ->assertSee('noindex,nofollow', false)
        ->assertDontSee('Whoops', false);
});

test('unauthorized admin access renders the branded forbidden page', function () {
    $manager = User::factory()->create();

    $this->actingAs($manager)
        ->get(route('admin.settings.edit'))
        ->assertForbidden()
        ->assertSee('لا تملك صلاحية الوصول')
        ->assertSee('contact', false);
});

test('throttled requests render the branded rate limit page', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('contact.store'), [
            'name' => 'عميل',
            'email' => 'customer@example.test',
            'message' => 'رسالة تجريبية',
        ]);
    }

    $this->post(route('contact.store'), [
        'name' => 'عميل',
        'email' => 'customer@example.test',
        'message' => 'رسالة تجريبية',
    ])->assertTooManyRequests()
        ->assertSee('طلبات كثيرة في وقت قصير');
});
