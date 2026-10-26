@php
    $aboutImage = store_setting('about_image');
    $aboutImageUrl = media_url($aboutImage);
    $pageSchema = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => $page->title,
            'url' => route('about'),
            'description' => $page->meta_description ?: store_setting('seo_description'),
        ],
    ];
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :og-image="$aboutImageUrl"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-6 sm:py-8">
            <x-store.breadcrumbs :items="[['label' => __('storefront.nav.about')]]" />
        </div>
    </div>

    <section class="relative isolate overflow-hidden bg-ink text-white">
        <span class="display-latin pointer-events-none absolute -bottom-10 -end-4 select-none text-[180px] leading-none text-white/[0.04] sm:text-[260px]" aria-hidden="true">{{ store_setting('store_name') }}</span>
        <div class="container-page relative py-14 sm:py-20">
            <p class="eyebrow-latin text-volt">{{ __('storefront.page.about_eyebrow') }}</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight sm:text-5xl">{{ $page->title }}</h1>
            <p class="mt-4 max-w-2xl text-sm leading-8 text-white/60">{{ store_setting('about_short') }}</p>

            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-volt btn-sm mt-7">
                    <x-store.icon name="whatsapp" class="size-4" />
                    {{ __('storefront.home.cta_button') }}
                </a>
            @endif
        </div>
    </section>

    <section class="container-page section-space">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)] lg:gap-16">
            <article>
                <div class="prose-ar max-w-3xl">
                    {!! $page->safe_content !!}
                </div>

                <p class="mt-8 border-t border-line pt-4 text-[11px] text-muted">
                    {{ __('storefront.page.updated_at', ['date' => $page->updated_at?->isoFormat('D MMMM Y') ?? '']) }}
                </p>
            </article>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                @if ($aboutImageUrl)
                    <div class="overflow-hidden rounded-2xl bg-smoke">
                        <img
                            src="{{ $aboutImageUrl }}"
                            @if (media_srcset($aboutImage)) srcset="{{ media_srcset($aboutImage) }}" sizes="(min-width: 1024px) 35vw, 100vw" @endif
                            alt="{{ __('storefront.home.about_image_alt') }}"
                            width="1792"
                            height="1024"
                            loading="lazy"
                            class="aspect-[4/3] size-full object-cover"
                        >
                    </div>
                @endif

                <div class="surface-card p-6">
                    <h2 class="text-lg font-extrabold">{{ store_setting('whatsapp_cta_title') }}</h2>
                    <p class="mt-2 text-xs leading-6 text-muted">{{ store_setting('whatsapp_cta_text') }}</p>
                    <div class="mt-4 flex flex-col gap-2">
                        @if ($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary btn-sm w-full">
                                <x-store.icon name="whatsapp" class="size-4 text-volt" />
                                {{ __('storefront.home.cta_button') }}
                            </a>
                        @endif
                        <a href="{{ route('contact') }}" class="btn-outline btn-sm w-full">{{ __('storefront.page.about_contact_cta') }}</a>
                        <a href="{{ route('products.index') }}" class="btn-ghost btn-sm w-full">{{ __('storefront.page.about_cta') }}</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-volt-dark/40 bg-volt p-6 text-ink">
                    <p class="eyebrow-latin">{{ __('storefront.home.steps_eyebrow') }}</p>
                    <ol class="mt-3 space-y-2 text-xs font-semibold leading-6">
                        <li>1. {{ __('storefront.home.step_1_title') }}</li>
                        <li>2. {{ __('storefront.home.step_2_title') }}</li>
                        <li>3. {{ __('storefront.home.step_3_title') }}</li>
                    </ol>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.storefront>
