<?php

use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

test('general settings normalize the WhatsApp number', function () {
    $this->actingAs($this->admin)->put(route('admin.settings.update'), [
        'group' => 'general',
        'store_name' => 'SQUAD',
        'whatsapp_number' => '+966 (50) 123-4567',
        'currency_code' => 'SAR',
        'currency_symbol' => 'ر.س',
    ])->assertSessionHasNoErrors();

    expect(StoreSetting::get('whatsapp_number'))->toBe('966501234567');
});

test('invalid WhatsApp numbers are rejected', function () {
    StoreSetting::set('whatsapp_number', '966500000000');

    $this->actingAs($this->admin)->put(route('admin.settings.update'), [
        'group' => 'general',
        'whatsapp_number' => '0501234567',
    ])->assertSessionHasErrors('whatsapp_number');

    expect(StoreSetting::get('whatsapp_number'))->toBe('966500000000');
});

test('unchecked toggles are stored as disabled', function () {
    StoreSetting::set('show_announcement', '1');

    $this->actingAs($this->admin)->put(route('admin.settings.update'), [
        'group' => 'general',
        'store_name' => 'SQUAD',
    ])->assertSessionHasNoErrors();

    expect(StoreSetting::bool('show_announcement'))->toBeFalse();
});

test('content managers can update homepage settings but not general settings', function () {
    $contentManager = User::factory()->contentManager()->create();

    $this->actingAs($contentManager)->put(route('admin.settings.update'), [
        'group' => 'homepage',
        'hero_title' => 'عنوان جديد',
    ])->assertSessionHasNoErrors();

    expect(StoreSetting::get('hero_title'))->toBe('عنوان جديد');

    $this->actingAs($contentManager)->put(route('admin.settings.update'), [
        'group' => 'general',
        'store_name' => 'مخترق',
    ])->assertForbidden();

    expect(StoreSetting::get('store_name'))->not->toBe('مخترق');
});

test('brand images are uploaded to the branding folder', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->put(route('admin.settings.update'), [
        'group' => 'general',
        'store_name' => 'SQUAD',
        'logo' => UploadedFile::fake()->image('logo.png', 600, 600),
    ])->assertSessionHasNoErrors();

    $path = StoreSetting::get('logo');

    expect($path)->toStartWith('branding/')
        ->and($path)->toEndWith('.png');

    Storage::disk('public')->assertExists($path);
});

test('settings reject invalid social URLs', function () {
    $this->actingAs($this->admin)->put(route('admin.settings.update'), [
        'group' => 'general',
        'store_name' => 'SQUAD',
        'social_instagram' => 'not-a-url',
    ])->assertSessionHasErrors('social_instagram');
});
