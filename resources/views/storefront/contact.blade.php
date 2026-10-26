@php
    $phone = store_setting('contact_phone');
    $phoneHref = preg_replace('/[^\d+]/', '', $phone);
    $email = store_setting('contact_email');
    $address = store_setting('address');
    $hours = store_setting('business_hours');
    $socials = collect([
        ['key' => 'instagram', 'label' => __('storefront.footer.instagram'), 'url' => safe_public_url(store_setting('social_instagram')), 'aria' => __('storefront.aria.social_instagram')],
        ['key' => 'tiktok', 'label' => __('storefront.footer.tiktok'), 'url' => safe_public_url(store_setting('social_tiktok')), 'aria' => __('storefront.aria.social_tiktok')],
        ['key' => 'x-social', 'label' => __('storefront.footer.x'), 'url' => safe_public_url(store_setting('social_x')), 'aria' => __('storefront.aria.social_x')],
    ])->filter(fn ($social) => $social['url'] !== null);
    $privacyPage = $footerPages->firstWhere('slug', 'privacy-policy');
    $hasDirectContact = filled($phone) || filled($email) || filled($address) || filled($hours);

    $contactPoint = array_filter([
        'telephone' => $phone ?: null,
        'email' => $email ?: null,
        'contactType' => 'customer service',
    ]);
    $pageSchema = [
        array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => __('storefront.contact.title'),
            'url' => route('contact'),
            'description' => __('storefront.contact.meta_description'),
            'mainEntity' => $contactPoint !== []
                ? array_merge(['@type' => 'Organization', 'name' => store_setting('store_name')], $contactPoint)
                : null,
        ]),
    ];
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-6 sm:py-8">
            <x-store.breadcrumbs :items="[['label' => __('storefront.nav.contact')]]" />
        </div>
    </div>

    <section class="container-page py-10 sm:py-14">
        <div class="max-w-2xl">
            <p class="eyebrow flex items-center gap-2.5">
                <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                {{ __('storefront.contact.eyebrow') }}
            </p>
            <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ __('storefront.contact.title') }}</h1>
            <p class="mt-3 text-sm leading-7 text-muted">{{ __('storefront.contact.intro') }}</p>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-12">
            <div class="space-y-4">
                @if ($whatsappUrl)
                    <div class="relative isolate overflow-hidden rounded-2xl bg-ink p-6 text-white sm:p-7">
                        <span class="pointer-events-none absolute -bottom-8 -end-2 select-none text-[110px] leading-none text-white/[0.05]" aria-hidden="true">WA</span>
                        <span class="flex size-11 items-center justify-center rounded-full bg-whatsapp/15 text-whatsapp">
                            <x-store.icon name="whatsapp" class="size-6" />
                        </span>
                        <h2 class="mt-5 text-lg font-extrabold">{{ __('storefront.contact.whatsapp_title') }}</h2>
                        <p class="mt-2 text-xs leading-6 text-white/60">{{ __('storefront.contact.whatsapp_text') }}</p>
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-volt btn-sm mt-5">
                            <x-store.icon name="whatsapp" class="size-4" />
                            {{ __('storefront.contact.whatsapp_cta') }}
                        </a>
                    </div>
                @endif

                @if ($hasDirectContact)
                    <div class="surface-card divide-y divide-line">
                        @if (filled($phone))
                            <a href="tel:{{ $phoneHref }}" class="flex items-start gap-4 p-5 transition hover:bg-smoke/60">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-smoke text-ink">
                                    <x-store.icon name="phone" class="size-4" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[11px] font-bold text-muted">{{ __('storefront.contact.phone_title') }}</span>
                                    <span class="mt-0.5 block text-sm font-extrabold text-ink" dir="ltr">{{ $phone }}</span>
                                </span>
                            </a>
                        @endif

                        @if (filled($email))
                            <a href="mailto:{{ $email }}" class="flex items-start gap-4 p-5 transition hover:bg-smoke/60">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-smoke text-ink">
                                    <x-store.icon name="mail" class="size-4" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[11px] font-bold text-muted">{{ __('storefront.contact.email_title') }}</span>
                                    <span class="mt-0.5 block break-all text-sm font-extrabold text-ink" dir="ltr">{{ $email }}</span>
                                </span>
                            </a>
                        @endif

                        @if (filled($address))
                            <div class="flex items-start gap-4 p-5">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-smoke text-ink">
                                    <x-store.icon name="pin" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-muted">{{ __('storefront.contact.address_title') }}</p>
                                    <p class="mt-0.5 whitespace-pre-line text-sm font-semibold leading-6 text-ink">{{ $address }}</p>
                                </div>
                            </div>
                        @endif

                        @if (filled($hours))
                            <div class="flex items-start gap-4 p-5">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-smoke text-ink">
                                    <x-store.icon name="clock" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-muted">{{ __('storefront.contact.hours_title') }}</p>
                                    <p class="mt-0.5 text-sm font-semibold leading-6 text-ink">{{ $hours }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif (! $whatsappUrl)
                    <div class="rounded-2xl border border-dashed border-line bg-smoke/40 p-6 text-sm leading-7 text-muted">
                        {{ __('storefront.contact.unavailable') }}
                    </div>
                @endif

                @if ($socials->isNotEmpty())
                    <div class="surface-card p-5">
                        <h2 class="text-sm font-extrabold">{{ __('storefront.contact.social_title') }}</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($socials as $social)
                                <a
                                    href="{{ $social['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="chip py-1.5! text-[11px]"
                                    aria-label="{{ $social['aria'] }}"
                                >
                                    <x-store.icon :name="$social['key']" class="size-3.5" />
                                    {{ $social['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="surface-card p-6 sm:p-8">
                <h2 class="text-xl font-extrabold">{{ __('storefront.contact.form_title') }}</h2>
                <p class="mt-1.5 text-xs leading-6 text-muted">{{ __('storefront.contact.form_intro') }}</p>

                <form method="POST" action="{{ route('contact.store') }}" class="mt-6">
                    @csrf

                    <div class="pointer-events-none absolute -start-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="field-label">{{ __('storefront.contact.name') }} <span class="text-danger">*</span></label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                autocomplete="name"
                                @class(['field', 'border-danger' => $errors->has('name')])
                                @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                            >
                            @error('name')
                                <p id="name-error" class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="field-label">{{ __('storefront.contact.phone') }} <span class="font-normal text-muted">({{ __('storefront.common.optional') }})</span></label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                dir="ltr"
                                @class(['field', 'border-danger' => $errors->has('phone')])
                                @if ($errors->has('phone')) aria-invalid="true" aria-describedby="phone-error" @endif
                            >
                            @error('phone')
                                <p id="phone-error" class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="email" class="field-label">{{ __('storefront.contact.email') }} <span class="text-danger">*</span></label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            dir="ltr"
                            placeholder="{{ __('storefront.contact.email_placeholder') }}"
                            @class(['field', 'border-danger' => $errors->has('email')])
                            @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                        >
                        @error('email')
                            <p id="email-error" class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="message" class="field-label">{{ __('storefront.contact.message') }} <span class="text-danger">*</span></label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            placeholder="{{ __('storefront.contact.message_placeholder') }}"
                            @class(['field resize-y', 'border-danger' => $errors->has('message')])
                            @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p id="message-error" class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary mt-5 w-full sm:w-auto">
                        <x-store.icon name="mail" class="size-4" />
                        {{ __('storefront.contact.send') }}
                    </button>

                    <p class="mt-3 text-[10px] leading-5 text-muted">
                        {{ __('storefront.contact.privacy_note') }}
                        @if ($privacyPage)
                            <a href="{{ route('pages.show', $privacyPage) }}" class="font-bold text-ink underline decoration-ink/25 underline-offset-2 hover:decoration-ink">{{ $privacyPage->title }}</a>
                        @endif
                    </p>
                    <p class="mt-1 text-[10px] leading-5 text-muted">{{ __('storefront.contact.form_note') }}</p>
                </form>
            </div>
        </div>
    </section>
</x-layouts.storefront>
