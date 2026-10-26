@php
    $id = 'setting-'.str_replace(['.', '_'], '-', $key);
    $fieldId = str_replace(['.', '_'], '-', $key);
    $hints = [
        'whatsapp_number' => __('admin.settings.whatsapp_hint'),
        'announcement_text' => __('admin.settings.announcement_hint'),
        'social_instagram' => __('admin.settings.social_hint'),
        'social_tiktok' => __('admin.settings.social_hint'),
        'social_x' => __('admin.settings.social_hint'),
        'logo' => __('admin.settings.logo_hint'),
        'favicon' => __('admin.settings.favicon_hint'),
        'hero_image' => __('admin.settings.hero_image_hint'),
        'about_image' => __('admin.settings.about_image_hint'),
        'footer_note' => __('admin.settings.footer_note_hint'),
        'seo_title' => __('admin.settings.seo_hint'),
        'seo_description' => __('admin.settings.seo_hint'),
    ];
    $hint = $hints[$key] ?? null;
@endphp

@if ($definition['type'] === 'toggle')
    <x-admin.toggle
        :name="$key"
        :label="$label"
        :help="$hint ?? __('admin.settings.show_section')"
        :checked="$value === '1'"
        :id="$id"
    />
@elseif ($definition['type'] === 'image' || $definition['type'] === 'raw_image')
    <div>
        <span class="field-label">{{ $label }}</span>
        @if ($hint)
            <p class="-mt-1 mb-2 text-xs leading-5 text-muted">{{ $hint }}</p>
        @endif
        <div class="flex items-center gap-3 rounded-xl border border-line bg-smoke/40 p-3">
            <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-line bg-white">
                @if ($value !== '')
                    <img src="{{ media_url($value) }}" alt="" class="size-full object-contain p-1" loading="lazy">
                @else
                    <x-store.icon name="image" class="size-5 text-muted" />
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <label for="{{ $id }}" class="block cursor-pointer text-xs font-bold">{{ $value !== '' ? __('admin.settings.replace_image') : __('admin.settings.field_'.$key) }}</label>
                <input id="{{ $id }}" type="file" name="{{ $key }}" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-[11px] text-muted file:me-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-ink file:px-3 file:py-1.5 file:text-[11px] file:font-bold file:text-white">
            </div>
        </div>
        @error($key)
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>
@elseif ($definition['type'] === 'textarea')
    <div class="sm:col-span-2">
        <x-admin.field
            :name="$key"
            type="textarea"
            :label="$label"
            :value="$value"
            :help="$hint"
            rows="3"
            maxlength="2000"
        />
    </div>
@else
    <x-admin.field
        :name="$key"
        :type="$definition['type'] === 'url' ? 'url' : 'text'"
        :label="$label"
        :value="$value"
        :help="$hint"
        :dir="$definition['type'] === 'url' || in_array($key, ['whatsapp_number', 'contact_phone', 'contact_email', 'currency_code'], true) ? 'ltr' : 'auto'"
        :maxlength="$key === 'currency_code' ? 3 : 255"
    />
@endif
