@php
    $storeName = store_setting('store_name');
    $heroImage = store_setting('hero_image');
    $heroImageUrl = media_url($heroImage);
    $aboutImage = store_setting('about_image');
    $aboutImageUrl = media_url($aboutImage);
    $socialLinks = collect([store_setting('social_instagram'), store_setting('social_tiktok'), store_setting('social_x')])
        ->map(fn ($url) => safe_public_url($url))
        ->filter()
        ->values()
        ->all();

    $schema = [
        array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $storeName,
            'url' => route('home'),
            'logo' => media_url(store_setting('logo')),
            'sameAs' => $socialLinks,
        ]),
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $storeName,
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('products.index').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ];

    $valueItems = array_values(array_filter(array_map('trim', explode('|', __('storefront.home.values_strip')))));
    $heroTitle = store_setting('hero_title');
    $heroHighlight = store_setting('hero_title_highlight');
    $heroSubtitle = store_setting('hero_subtitle');
    $heroCta = store_setting('hero_cta_label');
@endphp

<x-layouts.storefront
    :title="store_setting('seo_title')"
    :description="store_setting('seo_description')"
    :canonical="route('home')"
    :og-image="$heroImageUrl"
    og-type="website"
    :schema="$schema"
    :preload-image="store_boolean('show_hero') ? $heroImage : null"
>
    @if (store_boolean('show_hero'))
        <section class="relative isolate overflow-hidden bg-ink text-white">
            @if ($heroImageUrl)
                <div class="absolute inset-0">
                    <img
                        src="{{ $heroImageUrl }}"
                        @if (media_srcset($heroImage)) srcset="{{ media_srcset($heroImage) }}" sizes="100vw" @endif
                        alt="{{ store_setting('hero_image_alt') ?: __('storefront.home.hero_image_alt') }}"
                        width="1792"
                        height="1024"
                        class="size-full object-cover"
                        fetchpriority="high"
                    >
                    <div class="absolute inset-0 bg-ink/65 lg:hidden" aria-hidden="true"></div>
                    <div class="absolute inset-0 hidden lg:block" style="background-image: linear-gradient(to left, #0b0b0c 0%, rgba(11,11,12,.92) 32%, rgba(11,11,12,.55) 64%, rgba(11,11,12,.18) 100%);" aria-hidden="true"></div>
                </div>
            @else
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,#242528_0%,#0b0b0c_55%)]" aria-hidden="true"></div>
            @endif

            <span
                class="display-latin pointer-events-none absolute -bottom-10 start-4 hidden select-none text-[180px] leading-none text-white/[0.035] lg:block"
                aria-hidden="true"
            >{{ $storeName }}</span>

            <div class="container-page relative flex min-h-[580px] items-center py-16 sm:min-h-[620px] lg:min-h-[680px] lg:py-20">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3" data-reveal>
                        <span class="h-px w-8 bg-volt" aria-hidden="true"></span>
                        <p class="eyebrow-latin text-volt">{{ store_setting('hero_eyebrow') }}</p>
                    </div>

                    <h1 class="mt-5 text-[42px] font-extrabold leading-[1.12] tracking-tight sm:text-6xl lg:text-[68px]" data-reveal>
                        {{ $heroTitle }}
                        @if (filled($heroHighlight))
                            <span class="block text-volt">{{ $heroHighlight }}</span>
                        @endif
                    </h1>

                    <p class="mt-5 max-w-xl text-sm leading-8 text-white/70 sm:text-base" data-reveal>
                        {{ $heroSubtitle }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3" data-reveal>
                        <a href="{{ route('products.index') }}" class="btn-volt btn-lg">
                            {{ $heroCta }}
                            <x-store.icon name="arrow-left" class="size-4" />
                        </a>
                        @if ($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-lg border border-white/25 text-white hover:border-white hover:bg-white hover:text-ink">
                                <x-store.icon name="whatsapp" class="size-5" />
                                {{ __('storefront.home.cta_button') }}
                            </a>
                        @else
                            <a href="{{ route('contact') }}" class="btn btn-lg border border-white/25 text-white hover:border-white hover:bg-white hover:text-ink">
                                {{ __('storefront.home.hero_secondary_cta') }}
                            </a>
                        @endif
                    </div>

                    <dl class="mt-10 grid max-w-lg grid-cols-3 gap-3 border-t border-white/15 pt-6 text-[11px] font-semibold text-white/60 sm:text-xs" data-reveal>
                        <div class="flex items-center gap-2">
                            <x-store.icon name="whatsapp" class="size-4 shrink-0 text-volt" />
                            <dt>{{ __('storefront.home.hero_stat_1') }}</dt>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-store.icon name="bag" class="size-4 shrink-0 text-volt" />
                            <dt>{{ __('storefront.home.hero_stat_2') }}</dt>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-store.icon name="shield" class="size-4 shrink-0 text-volt" />
                            <dt>{{ __('storefront.home.hero_stat_3') }}</dt>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        @if ($valueItems !== [])
            <div class="border-y border-volt-dark/40 bg-volt text-ink">
                <div class="container-page grid grid-cols-2 gap-x-5 gap-y-3 py-4 sm:flex sm:items-center sm:justify-between sm:gap-6">
                    @foreach ($valueItems as $item)
                        <p class="flex items-center gap-2 text-[11px] font-bold sm:text-xs">
                            <x-store.icon name="check" class="size-4 shrink-0" />
                            {{ $item }}
                        </p>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    @if (store_boolean('show_featured_categories') && $featuredCategories->isNotEmpty())
        <section class="section-space">
            <div class="container-page">
                <x-store.section-heading
                    :eyebrow="__('storefront.home.categories_eyebrow')"
                    :title="store_setting('featured_categories_title')"
                    :subtitle="store_setting('featured_categories_subtitle')"
                    :action-url="route('products.index')"
                    :action-label="__('storefront.common.view_all')"
                />

                <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($featuredCategories as $category)
                        <a
                            href="{{ route('categories.show', $category) }}"
                            class="group relative isolate block aspect-[4/5] overflow-hidden rounded-2xl bg-smoke"
                            data-reveal
                        >
                            @if ($category->image_path)
                                <img
                                    src="{{ media_url($category->image_path) }}"
                                    @if (media_srcset($category->image_path)) srcset="{{ media_srcset($category->image_path) }}" sizes="(min-width: 1024px) 25vw, 50vw" @endif
                                    alt="{{ $category->name }}"
                                    width="1024"
                                    height="1280"
                                    loading="lazy"
                                    class="size-full object-cover transition duration-700 group-hover:scale-[1.04]"
                                >
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/20 to-transparent" aria-hidden="true"></div>
                            <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-4 sm:p-5">
                                <div>
                                    <p class="text-[10px] font-bold text-volt">{{ __('storefront.category.eyebrow') }}</p>
                                    <h3 class="mt-1 text-lg font-extrabold text-white sm:text-xl">{{ $category->name }}</h3>
                                </div>
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-white/30 text-white transition group-hover:border-volt group-hover:bg-volt group-hover:text-ink">
                                    <x-store.icon name="arrow-left" class="size-4" />
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (store_boolean('show_featured_products') && $featuredProducts->isNotEmpty())
        <section class="pb-14 sm:pb-20">
            <div class="container-page">
                <x-store.section-heading
                    :eyebrow="__('storefront.home.featured_eyebrow')"
                    :title="store_setting('featured_products_title')"
                    :subtitle="__('storefront.home.featured_subtitle')"
                    :action-url="route('products.index', ['featured' => 1])"
                    :action-label="__('storefront.common.view_all')"
                />

                <div class="mt-10 grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <x-store.product-card :product="$product" :eager="$loop->first" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($banners->isNotEmpty())
        <section class="pb-14 sm:pb-20">
            <div class="container-page grid gap-3 sm:gap-5 lg:grid-cols-2">
                @foreach ($banners as $banner)
                    @php
                        $bannerUrl = safe_public_url($banner->link_url);
                        $bannerImage = media_url($banner->image_path);
                    @endphp
                    <div class="group relative isolate min-h-[240px] overflow-hidden rounded-2xl bg-ink sm:min-h-[300px]" data-reveal>
                        @if ($bannerImage)
                            <img
                                src="{{ $bannerImage }}"
                                @if (media_srcset($banner->image_path)) srcset="{{ media_srcset($banner->image_path) }}" sizes="(min-width: 1024px) 50vw, 100vw" @endif
                                alt="{{ $banner->title }}"
                                width="1792"
                                height="1024"
                                loading="lazy"
                                class="absolute inset-0 size-full object-cover opacity-80 transition duration-700 group-hover:scale-[1.03]"
                            >
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-ink/10" aria-hidden="true"></div>
                        <div class="relative flex h-full min-h-[240px] flex-col justify-end p-6 text-white sm:min-h-[300px] sm:p-8">
                            <p class="eyebrow-latin text-volt">{{ __('storefront.home.promo_eyebrow') }}</p>
                            <h3 class="mt-2 max-w-md text-2xl font-extrabold sm:text-3xl">{{ $banner->title }}</h3>
                            @if (filled($banner->subtitle))
                                <p class="mt-2 max-w-md text-xs leading-6 text-white/70 sm:text-sm">{{ $banner->subtitle }}</p>
                            @endif
                            @if ($bannerUrl)
                                <a href="{{ $bannerUrl }}" class="btn-white btn-sm mt-5 self-start">
                                    {{ $banner->button_label ?: __('storefront.home.promo_default_button') }}
                                    <x-store.icon name="arrow-left" class="size-4" />
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if (store_boolean('show_new_arrivals') && $newArrivals->isNotEmpty())
        <section class="bg-smoke py-14 sm:py-20">
            <div class="container-page">
                <x-store.section-heading
                    :eyebrow="__('storefront.home.new_eyebrow')"
                    :title="store_setting('new_arrivals_title')"
                    :subtitle="__('storefront.home.new_subtitle')"
                    :action-url="route('products.index', ['sort' => 'newest'])"
                    :action-label="__('storefront.common.view_all')"
                />

                <div class="mt-10 grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-4">
                    @foreach ($newArrivals as $product)
                        <x-store.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (store_boolean('show_offers') && $offers->isNotEmpty())
        <section class="section-space">
            <div class="container-page">
                <x-store.section-heading
                    :eyebrow="__('storefront.home.offers_eyebrow')"
                    :title="store_setting('offers_title')"
                    :subtitle="__('storefront.home.offers_subtitle')"
                    :action-url="route('products.index', ['on_sale' => 1])"
                    :action-label="__('storefront.common.view_all')"
                />

                <div class="mt-10 grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-4">
                    @foreach ($offers as $product)
                        <x-store.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (store_boolean('show_about'))
        <section class="pb-14 sm:pb-20">
            <div class="container-page">
                <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div class="relative order-2 lg:order-1" data-reveal>
                        @if ($aboutImageUrl)
                            <div class="overflow-hidden rounded-2xl bg-smoke">
                                <img
                                    src="{{ $aboutImageUrl }}"
                                    @if (media_srcset($aboutImage)) srcset="{{ media_srcset($aboutImage) }}" sizes="(min-width: 1024px) 50vw, 100vw" @endif
                                    alt="{{ __('storefront.home.about_image_alt') }}"
                                    width="1792"
                                    height="1024"
                                    loading="lazy"
                                    class="aspect-[4/3] size-full object-cover"
                                >
                            </div>
                        @else
                            <div class="flex aspect-[4/3] items-center justify-center rounded-2xl bg-ink text-white/20">
                                <span class="display-latin text-6xl">{{ $storeName }}</span>
                            </div>
                        @endif
                        <div class="absolute -bottom-4 -start-2 hidden rounded-xl bg-volt px-5 py-4 text-ink shadow-sm sm:block">
                            <p class="display-latin text-3xl leading-none">{{ now()->year }}</p>
                            <p class="mt-1 text-[10px] font-bold">{{ __('storefront.home.hero_badge', ['year' => now()->year]) }}</p>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2">
                        <p class="eyebrow flex items-center gap-2.5" data-reveal>
                            <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                            {{ __('storefront.home.about_eyebrow') }}
                        </p>
                        <h2 class="section-title mt-3" data-reveal>{{ store_setting('about_title') }}</h2>
                        <p class="mt-4 text-sm leading-8 text-muted sm:text-base" data-reveal>
                            {{ store_setting('about_short') }}
                        </p>

                        <ul class="mt-7 space-y-3" data-reveal>
                            @foreach (['about_point_1', 'about_point_2', 'about_point_3'] as $point)
                                <li class="flex items-start gap-3 text-sm font-semibold">
                                    <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-volt text-ink">
                                        <x-store.icon name="check" class="size-3.5" />
                                    </span>
                                    {{ __("storefront.home.$point") }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('about') }}" class="btn-primary mt-8">
                            {{ __('storefront.home.about_cta') }}
                            <x-store.icon name="arrow-left" class="size-4" />
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (store_boolean('show_why_us'))
        <section class="bg-ink py-14 text-white sm:py-20">
            <div class="container-page">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow flex items-center gap-2.5 text-white/50">
                            <span class="h-px w-7 bg-volt" aria-hidden="true"></span>
                            {{ __('storefront.home.why_eyebrow') }}
                        </p>
                        <h2 class="section-title mt-3">{{ store_setting('why_title') }}</h2>
                        <p class="mt-3 text-sm leading-7 text-white/55">{{ __('storefront.home.why_subtitle') }}</p>
                    </div>
                    <a href="{{ route('faq') }}" class="btn btn-sm self-start border border-white/20 text-white hover:border-volt hover:text-volt sm:self-auto">
                        {{ __('storefront.nav.faq') }}
                        <x-store.icon name="arrow-left" class="size-4" />
                    </a>
                </div>

                <div class="mt-10 grid gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['icon' => 'whatsapp', 'title' => 'feature_whatsapp_title', 'text' => 'feature_whatsapp_text'],
                        ['icon' => 'shield', 'title' => 'feature_confirm_title', 'text' => 'feature_confirm_text'],
                        ['icon' => 'bag', 'title' => 'feature_payment_title', 'text' => 'feature_payment_text'],
                        ['icon' => 'ruler', 'title' => 'feature_sizing_title', 'text' => 'feature_sizing_text'],
                    ] as $feature)
                        <div class="bg-ink p-6 transition hover:bg-carbon sm:p-7" data-reveal>
                            <span class="flex size-11 items-center justify-center rounded-full bg-volt/10 text-volt">
                                <x-store.icon :name="$feature['icon']" class="size-5" />
                            </span>
                            <h3 class="mt-5 text-base font-extrabold">{{ __("storefront.home.{$feature['title']}") }}</h3>
                            <p class="mt-2 text-xs leading-6 text-white/55">{{ __("storefront.home.{$feature['text']}") }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-14 grid gap-8 border-t border-white/10 pt-10 sm:grid-cols-3">
                    @foreach ([
                        ['number' => '01', 'title' => 'step_1_title', 'text' => 'step_1_text'],
                        ['number' => '02', 'title' => 'step_2_title', 'text' => 'step_2_text'],
                        ['number' => '03', 'title' => 'step_3_title', 'text' => 'step_3_text'],
                    ] as $step)
                        <div class="flex gap-4" data-reveal>
                            <span class="display-latin text-3xl leading-none text-volt">{{ $step['number'] }}</span>
                            <div>
                                <h3 class="text-sm font-extrabold">{{ __("storefront.home.{$step['title']}") }}</h3>
                                <p class="mt-1.5 text-xs leading-6 text-white/50">{{ __("storefront.home.{$step['text']}") }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (store_boolean('show_whatsapp_cta'))
        <section class="section-space">
            <div class="container-page">
                <div class="relative isolate overflow-hidden rounded-3xl bg-volt px-6 py-12 text-ink sm:px-12 sm:py-16">
                    <span class="display-latin pointer-events-none absolute -bottom-12 -end-4 select-none text-[190px] leading-none text-ink/[0.06]" aria-hidden="true">{{ $storeName }}</span>
                    <div class="relative max-w-2xl">
                        <p class="eyebrow-latin">{{ __('storefront.home.cta_eyebrow') }}</p>
                        <h2 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ store_setting('whatsapp_cta_title') }}</h2>
                        <p class="mt-3 text-sm leading-7 text-ink/70">{{ store_setting('whatsapp_cta_text') }}</p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            @if ($whatsappUrl)
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary btn-lg">
                                    <x-store.icon name="whatsapp" class="size-5 text-volt" />
                                    {{ __('storefront.home.cta_button') }}
                                </a>
                            @else
                                <a href="{{ route('contact') }}" class="btn-primary btn-lg">
                                    {{ __('storefront.nav.contact') }}
                                    <x-store.icon name="arrow-left" class="size-4" />
                                </a>
                            @endif
                            <a href="{{ route('products.index') }}" class="btn btn-lg border border-ink/20 hover:border-ink hover:bg-ink hover:text-white">
                                {{ __('storefront.home.cta_browse') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</x-layouts.storefront>
