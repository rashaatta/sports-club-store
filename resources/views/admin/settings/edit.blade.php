@php
    $generalSections = [
        'section_store' => ['store_name', 'store_tagline', 'announcement_text', 'show_announcement'],
        'section_contact' => ['whatsapp_number', 'contact_phone', 'contact_email', 'address', 'business_hours'],
        'section_social' => ['social_instagram', 'social_tiktok', 'social_x'],
        'section_currency' => ['currency_code', 'currency_symbol'],
        'section_logo' => ['logo', 'favicon'],
        'section_seo' => ['seo_title', 'seo_description', 'footer_note'],
    ];

    $homepageSections = [
        'section_hero' => ['show_hero', 'hero_eyebrow', 'hero_title', 'hero_title_highlight', 'hero_subtitle', 'hero_cta_label', 'hero_image', 'hero_image_alt'],
        'section_home_sections' => ['show_featured_categories', 'featured_categories_title', 'featured_categories_subtitle', 'show_featured_products', 'featured_products_title', 'show_new_arrivals', 'new_arrivals_title', 'show_offers', 'offers_title'],
        'section_about' => ['show_about', 'about_title', 'about_short', 'about_image'],
        'section_why' => ['show_why_us', 'why_title'],
        'section_cta' => ['show_whatsapp_cta', 'whatsapp_cta_title', 'whatsapp_cta_text'],
    ];

    $sectionLabels = [
        'section_store' => __('admin.settings.section_store'),
        'section_contact' => __('admin.settings.section_contact'),
        'section_social' => __('admin.settings.section_social'),
        'section_currency' => __('admin.settings.section_currency'),
        'section_logo' => __('admin.settings.section_logo'),
        'section_seo' => __('admin.settings.section_seo'),
        'section_hero' => __('admin.settings.section_hero'),
        'section_home_sections' => __('admin.settings.section_home_sections'),
        'section_about' => __('admin.settings.section_about'),
        'section_why' => __('admin.settings.section_why'),
        'section_cta' => __('admin.settings.section_cta'),
    ];
@endphp

<x-layouts.admin :title="__('admin.settings.title')">
    <x-admin.page-header :title="__('admin.settings.title')" :subtitle="__('admin.settings.subtitle')">
        <x-slot:actions>
            @foreach ($groups as $groupKey => $group)
                <a href="#{{ $groupKey }}" class="btn-outline btn-sm">
                    <x-store.icon :name="$groupKey === 'homepage' ? 'home' : 'settings'" class="size-4" />
                    {{ __('admin.settings.'.$groupKey) }}
                </a>
            @endforeach
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-line/70 bg-white px-5 py-4 text-sm leading-6 text-muted shadow-panel">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-volt text-ink">
            <x-store.icon name="whatsapp" class="size-4" />
        </span>
        <p>{{ __('admin.settings.whatsapp_hint') }}</p>
    </div>

    <div class="space-y-8">
        @foreach ($groups as $groupKey => $group)
            @php
                $sections = $groupKey === 'general' ? $generalSections : $homepageSections;
                $fields = $group['fields'];
                $covered = collect($sections)->flatten()->all();
                $remaining = array_diff(array_keys($fields), $covered);
            @endphp

            <section id="{{ $groupKey }}" class="scroll-mt-24" aria-labelledby="settings-{{ $groupKey }}-title">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="group" value="{{ $groupKey }}">

                    <div class="surface-card overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-smoke/40 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <span class="flex size-10 items-center justify-center rounded-xl bg-ink text-volt">
                                    <x-store.icon :name="$groupKey === 'homepage' ? 'home' : 'sliders'" class="size-5" />
                                </span>
                                <div>
                                    <h2 id="settings-{{ $groupKey }}-title" class="text-base font-black">{{ __('admin.settings.'.$groupKey) }}</h2>
                                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.settings.'.$groupKey.'_hint') }}</p>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary btn-sm">
                                <x-store.icon name="check" class="size-4" />
                                {{ __('admin.common.save') }}
                            </button>
                        </div>

                        <div class="divide-y divide-line/70">
                            @foreach ($sections as $sectionKey => $keys)
                                <div class="px-5 py-5 sm:px-6">
                                    <h3 class="mb-4 flex items-center gap-2 text-xs font-black uppercase tracking-[0.14em] text-muted">
                                        <span class="h-px w-4 bg-volt"></span>
                                        {{ $sectionLabels[$sectionKey] ?? $sectionKey }}
                                    </h3>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        @foreach ($keys as $key)
                                            @if (isset($fields[$key]))
                                                @include('admin.settings._field', [
                                                    'key' => $key,
                                                    'definition' => $fields[$key],
                                                    'value' => (string) old($key, $values[$key] ?? ''),
                                                    'label' => __('admin.settings.field_'.$key),
                                                ])
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            @if ($remaining !== [])
                                <div class="px-5 py-5 sm:px-6">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        @foreach ($remaining as $key)
                                            @include('admin.settings._field', [
                                                'key' => $key,
                                                'definition' => $fields[$key],
                                                'value' => (string) old($key, $values[$key] ?? ''),
                                                'label' => __('admin.settings.field_'.$key),
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-smoke/30 px-5 py-4 sm:px-6">
                            <p class="text-[11px] text-muted">{{ __('admin.common.unsaved_hint') }}</p>
                            <button type="submit" class="btn-primary btn-sm">
                                <x-store.icon name="check" class="size-4" />
                                {{ __('admin.common.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        @endforeach
    </div>
</x-layouts.admin>
