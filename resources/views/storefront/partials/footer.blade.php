@php
    $storeName = store_setting('store_name');
    $phone = store_setting('contact_phone');
    $email = store_setting('contact_email');
    $address = store_setting('address');
    $hours = store_setting('business_hours');
    $phoneHref = preg_replace('/[^\d+]/', '', $phone);
    $socials = collect([
        ['key' => 'instagram', 'label' => __('storefront.footer.instagram'), 'url' => store_setting('social_instagram'), 'aria' => __('storefront.aria.social_instagram')],
        ['key' => 'tiktok', 'label' => __('storefront.footer.tiktok'), 'url' => store_setting('social_tiktok'), 'aria' => __('storefront.aria.social_tiktok')],
        ['key' => 'x-social', 'label' => __('storefront.footer.x'), 'url' => store_setting('social_x'), 'aria' => __('storefront.aria.social_x')],
    ])->filter(fn ($social) => safe_public_url($social['url']) !== null);
    $contactConfigured = filled($phone) || filled($email) || filled($address) || filled($hours);
    $footerNote = store_setting('footer_note');
@endphp

<footer class="mt-16 bg-ink text-white sm:mt-24">
    <div class="container-page py-14 sm:py-16">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="display-latin inline-block text-3xl text-white">{{ $storeName }}<span class="text-volt">.</span></a>
                <p class="mt-4 max-w-sm text-sm leading-7 text-white/60">{{ __('storefront.footer.about_text') }}</p>

                @if ($socials->isNotEmpty())
                    <div class="mt-5">
                        <p class="text-[11px] font-bold text-white/40">{{ __('storefront.common.follow_us') }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            @foreach ($socials as $social)
                                <a
                                    href="{{ safe_public_url($social['url']) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex size-10 items-center justify-center rounded-full border border-white/15 text-white/70 transition hover:border-volt hover:text-volt"
                                    aria-label="{{ $social['aria'] }}"
                                >
                                    <x-store.icon :name="$social['key']" class="size-4" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xs font-bold text-white">{{ __('storefront.footer.shop_title') }}</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                    <li><a href="{{ route('products.index') }}" class="transition hover:text-volt">{{ __('storefront.nav.shop') }}</a></li>
                    <li><a href="{{ route('products.index', ['on_sale' => 1]) }}" class="transition hover:text-volt">{{ __('storefront.nav.offers') }}</a></li>
                    <li><a href="{{ route('products.index', ['featured' => 1]) }}" class="transition hover:text-volt">{{ __('storefront.common.featured') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="transition hover:text-volt">{{ __('storefront.nav.faq') }}</a></li>
                    <li><a href="{{ route('about') }}" class="transition hover:text-volt">{{ __('storefront.nav.about') }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xs font-bold text-white">{{ __('storefront.nav.categories') }}</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                    @foreach ($navCategories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category) }}" class="transition hover:text-volt">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xs font-bold text-white">{{ __('storefront.footer.policies_title') }}</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                    @foreach ($footerPages as $page)
                        <li>
                            <a href="{{ route('pages.show', $page) }}" class="transition hover:text-volt">{{ $page->title }}</a>
                        </li>
                    @endforeach
                    @if ($footerPages->isEmpty())
                        <li class="text-white/35">{{ __('storefront.common.configured_soon') }}</li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xs font-bold text-white">{{ __('storefront.footer.contact_title') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-white/60">
                    @if ($whatsappUrl)
                        <li>
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-bold text-volt transition hover:text-white">
                                <x-store.icon name="whatsapp" class="size-4" />
                                {{ __('storefront.footer.whatsapp_cta') }}
                            </a>
                        </li>
                    @endif

                    @if (filled($phone))
                        <li>
                            <a href="tel:{{ $phoneHref }}" class="flex items-start gap-2 transition hover:text-white">
                                <x-store.icon name="phone" class="mt-0.5 size-4 shrink-0" />
                                <span dir="ltr" class="tabular-nums">{{ $phone }}</span>
                            </a>
                        </li>
                    @endif

                    @if (filled($email))
                        <li>
                            <a href="mailto:{{ $email }}" class="flex items-start gap-2 break-all transition hover:text-white">
                                <x-store.icon name="mail" class="mt-0.5 size-4 shrink-0" />
                                <span dir="ltr">{{ $email }}</span>
                            </a>
                        </li>
                    @endif

                    @if (filled($address))
                        <li class="flex items-start gap-2">
                            <x-store.icon name="pin" class="mt-0.5 size-4 shrink-0" />
                            <span class="whitespace-pre-line">{{ $address }}</span>
                        </li>
                    @endif

                    @if (filled($hours))
                        <li class="flex items-start gap-2">
                            <x-store.icon name="clock" class="mt-0.5 size-4 shrink-0" />
                            <span>{{ $hours }}</span>
                        </li>
                    @endif

                    @if (! $contactConfigured && ! $whatsappUrl)
                        <li class="text-white/35">{{ __('storefront.footer.contact_soon') }}</li>
                    @endif
                </ul>
            </div>
        </div>

        @if (filled($footerNote))
            <p class="mt-10 border-t border-white/10 pt-6 text-xs leading-6 text-white/40">{{ $footerNote }}</p>
        @endif
    </div>

    <div class="border-t border-white/10">
        <div class="container-page flex flex-col items-start justify-between gap-3 py-5 text-[11px] text-white/40 sm:flex-row sm:items-center">
            <p>{{ __('storefront.footer.rights', ['store' => $storeName]) }} © {{ now()->year }}</p>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <span>{{ __('storefront.footer.pay_note') }}</span>
                <a href="{{ route('sitemap') }}" class="transition hover:text-white" dir="ltr">sitemap.xml</a>
            </div>
        </div>
    </div>
</footer>
