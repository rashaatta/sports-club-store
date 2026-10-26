@php
    $storeName = store_setting('store_name');
    $category = $product->category;
    $hasGallery = filled($gallery->first()['src'] ?? null);
    $firstImagePath = $product->images->first()?->path;

    $crumbs = collect();
    if ($category?->parent) {
        $crumbs->push(['label' => $category->parent->name, 'url' => route('categories.show', $category->parent)]);
    }
    if ($category) {
        $crumbs->push(['label' => $category->name, 'url' => route('categories.show', $category)]);
    }
    $crumbs->push(['label' => $product->name]);

    $currencyCode = store_setting('currency_code') ?: 'SAR';
    $imageUrls = $gallery->pluck('src')->filter()->values()->all();
    $priceTiers = $variants
        ->pluck('price')
        ->map(fn ($price) => number_format((float) $price, 2, '.', ''))
        ->unique()
        ->values();
    $useAggregateOffer = $priceTiers->count() > 1;
    $lowestPrice = $priceTiers->min();
    $highestPrice = $priceTiers->max();

    $offer = array_filter([
        '@type' => $useAggregateOffer ? 'AggregateOffer' : 'Offer',
        'url' => $product->url,
        'priceCurrency' => $currencyCode,
        'availability' => $product->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
        'seller' => ['@type' => 'Organization', 'name' => $storeName],
        'price' => $useAggregateOffer ? null : ($priceTiers->first() ?? number_format((float) $product->price, 2, '.', '')),
        'lowPrice' => $useAggregateOffer ? $lowestPrice : null,
        'highPrice' => $useAggregateOffer ? $highestPrice : null,
        'offerCount' => $useAggregateOffer ? $variants->count() : null,
    ]);

    $pageSchema = [
        array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'url' => $product->url,
            'description' => Str::limit(trim(strip_tags($product->description ?: $product->short_description)), 600),
            'sku' => $product->sku,
            'image' => $imageUrls,
            'category' => $category?->name,
            'brand' => ['@type' => 'Brand', 'name' => $product->brand ?: $storeName],
            'offers' => $offer,
        ]),
    ];

    $orderConfig = [
        'variants' => $variants,
        'hasVariants' => $product->usesVariants(),
        'simpleStock' => (int) ($product->stock_quantity ?? 0),
        'maxQuantity' => (int) config('store.orders.max_quantity', 20),
        'lowStockThreshold' => max(1, (int) $product->low_stock_threshold),
        'currency' => store_setting('currency_symbol'),
        'orderUrl' => $orderUrl,
        'whatsappConfigured' => $whatsappConfigured,
        'basePriceFormatted' => $product->price_formatted,
        'messages' => [
            'selectOptions' => __('storefront.product.options_required'),
            'unavailable' => __('storefront.product.combination_unavailable'),
            'generic' => __('storefront.validation.order_failed'),
            'stockAvailable' => __('storefront.product.stock_available', ['count' => '__COUNT__']),
            'stockLow' => __('storefront.product.stock_low', ['count' => '__COUNT__']),
            'stockOut' => __('storefront.product.stock_out'),
            'maxHint' => __('storefront.product.max_quantity_hint', ['max' => '__MAX__']),
            'sizeAria' => __('storefront.product.size_aria', ['size' => '__SIZE__']),
            'colorAria' => __('storefront.product.color_aria', ['name' => '__NAME__']),
            'unavailableOption' => __('storefront.product.unavailable_option'),
        ],
    ];

    $shareText = $product->name.' — '.$product->url;
    $whatsappShare = 'https://wa.me/?text='.rawurlencode($shareText);
    $xShare = 'https://x.com/intent/post?text='.rawurlencode($product->name).'&url='.rawurlencode($product->url);
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :og-image="$ogImage"
    og-type="product"
    :schema="$pageSchema"
    :preload-image="$firstImagePath"
>
    <div class="container-page py-6 sm:py-8">
        <x-store.breadcrumbs :items="$crumbs" />
    </div>

    <section class="container-page">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-12">
            <div class="min-w-0">
                @if ($hasGallery)
                    <div
                        class="lg:sticky lg:top-24"
                        x-data="productGallery({ images: @js($gallery) })"
                        role="region"
                        aria-label="{{ __('storefront.common.gallery') }}"
                    >
                        <div class="relative">
                            <button
                                type="button"
                                class="product-media group relative block aspect-[4/5] w-full cursor-zoom-in rounded-2xl"
                                @click="openZoom()"
                                aria-label="{{ __('storefront.product.zoom_open') }}"
                            >
                                <img
                                    :src="current.src"
                                    :srcset="current.srcset || null"
                                    sizes="(min-width: 1024px) 45vw, 100vw"
                                    :alt="current.alt"
                                    width="1024"
                                    height="1280"
                                    class="size-full object-cover"
                                    fetchpriority="high"
                                >
                                <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-2 bg-gradient-to-t from-ink/50 to-transparent px-4 pb-4 pt-12 text-[11px] font-semibold text-white/90 transition sm:opacity-0 sm:group-hover:opacity-100">
                                    <x-store.icon name="zoom" class="size-4" />
                                    {{ __('storefront.product.zoom_hint') }}
                                </span>
                            </button>

                            <div class="pointer-events-none absolute inset-x-3 top-3 flex flex-wrap items-start justify-between gap-2">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($product->discount_percent)
                                        <span class="badge-volt">{{ __('storefront.common.discount', ['percent' => $product->discount_percent]) }}</span>
                                    @endif
                                    @if ($product->is_new_arrival)
                                        <span class="badge-ink">{{ __('storefront.common.new') }}</span>
                                    @endif
                                    @if (! $product->is_in_stock)
                                        <span class="badge-muted bg-white/90">{{ __('storefront.common.out_of_stock') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div x-show="images.length > 1" x-cloak class="pointer-events-none absolute inset-y-0 inset-x-3 flex items-center justify-between">
                                <button
                                    type="button"
                                    class="pointer-events-auto inline-flex size-10 items-center justify-center rounded-full bg-white/90 text-ink shadow-sm backdrop-blur transition hover:bg-ink hover:text-white"
                                    @click="previous()"
                                    aria-label="{{ __('storefront.product.gallery_previous') }}"
                                >
                                    <x-store.icon name="chevron-end" class="size-4" />
                                </button>
                                <button
                                    type="button"
                                    class="pointer-events-auto inline-flex size-10 items-center justify-center rounded-full bg-white/90 text-ink shadow-sm backdrop-blur transition hover:bg-ink hover:text-white"
                                    @click="next()"
                                    aria-label="{{ __('storefront.product.gallery_next') }}"
                                >
                                    <x-store.icon name="chevron-start" class="size-4" />
                                </button>
                            </div>
                        </div>

                        <p class="sr-only" aria-live="polite" x-text="`${active + 1} / ${images.length} — ${current.alt}`"></p>

                        @if ($gallery->count() > 1)
                            <div class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-6">
                                <template x-for="(image, index) in images" :key="index">
                                    <button
                                        type="button"
                                        class="overflow-hidden rounded-lg border-2 bg-smoke transition"
                                        :class="active === index ? 'border-ink' : 'border-transparent hover:border-ink/25'"
                                        @click="select(index)"
                                        :aria-pressed="(active === index).toString()"
                                        :aria-label="image.alt"
                                    >
                                        <img :src="image.src" alt="" width="160" height="200" loading="lazy" class="aspect-[4/5] size-full object-cover">
                                    </button>
                                </template>
                            </div>
                        @endif

                        <div
                            x-show="zoom"
                            x-cloak
                            class="fixed inset-0 z-[80] flex items-center justify-center bg-ink/95 p-4 sm:p-10"
                            role="dialog"
                            aria-modal="true"
                            aria-label="{{ __('storefront.product.zoom_open') }}"
                            @keydown.escape.window="closeZoom()"
                            @keydown.tab="trapFocus"
                        >
                            <button
                                type="button"
                                class="absolute end-4 top-4 inline-flex size-11 items-center justify-center rounded-full border border-white/20 text-white transition hover:bg-white hover:text-ink"
                                @click="closeZoom()"
                                x-ref="zoomClose"
                                aria-label="{{ __('storefront.product.zoom_close') }}"
                            >
                                <x-store.icon name="close" class="size-5" />
                            </button>

                            <div class="relative flex max-h-full w-full max-w-4xl items-center justify-center" @click.self="closeZoom()">
                                <img :src="current.full || current.src" :alt="current.alt" class="max-h-[82vh] w-auto rounded-xl object-contain">
                                <div x-show="images.length > 1" class="absolute inset-y-0 inset-x-0 flex items-center justify-between">
                                    <button type="button" class="inline-flex size-11 items-center justify-center rounded-full bg-white/90 text-ink transition hover:bg-volt" @click="previous()" aria-label="{{ __('storefront.product.gallery_previous') }}">
                                        <x-store.icon name="chevron-end" class="size-5" />
                                    </button>
                                    <button type="button" class="inline-flex size-11 items-center justify-center rounded-full bg-white/90 text-ink transition hover:bg-volt" @click="next()" aria-label="{{ __('storefront.product.gallery_next') }}">
                                        <x-store.icon name="chevron-start" class="size-5" />
                                    </button>
                                </div>
                            </div>

                            <p class="absolute bottom-5 left-1/2 -translate-x-1/2 text-xs font-semibold text-white/60" aria-hidden="true">
                                <span x-text="active + 1"></span> / <span x-text="images.length"></span>
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex aspect-[4/5] flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-line bg-smoke text-muted">
                        <x-store.icon name="image" class="size-10" />
                        <p class="text-xs">{{ __('storefront.product.no_image') }}</p>
                    </div>
                @endif

                @if ($modelUrl)
                    <x-store.product-model :model="[
                        'src' => $modelUrl,
                        'alt' => $product->name,
                        'poster' => $ogImage,
                    ]" />
                @endif
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($category)
                        <a href="{{ route('categories.show', $category) }}" class="chip py-1.5! text-[11px]">{{ $category->name }}</a>
                    @endif
                    @if ($product->brand)
                        <span class="text-[11px] font-semibold text-muted">{{ $product->brand }}</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $product->name }}</h1>

                @if (filled($product->short_description))
                    <p class="mt-3 text-sm leading-7 text-muted">{{ $product->short_description }}</p>
                @endif

                <div class="mt-4 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="text-2xl font-extrabold text-ink">{{ $product->price_formatted }}</span>
                    @if ($product->compare_at_price)
                        <span class="text-base font-semibold text-muted line-through">{{ $product->compare_at_formatted }}</span>
                    @endif
                    @if ($product->discount_percent)
                        <span class="badge-volt">{{ __('storefront.common.discount', ['percent' => $product->discount_percent]) }}</span>
                    @endif
                </div>
                <p class="mt-1 text-[11px] text-muted">{{ __('storefront.product.price_note') }}</p>

                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-muted">
                    <span>
                        {{ __('storefront.product.sku_label') }}:
                        <span dir="ltr" class="font-semibold text-ink">{{ $product->sku }}</span>
                    </span>
                    <span class="h-3 w-px bg-line" aria-hidden="true"></span>
                    <span @class(['inline-flex items-center gap-1.5 font-bold', 'text-success' => $product->is_in_stock, 'text-danger' => ! $product->is_in_stock])>
                        <x-store.icon :name="$product->is_in_stock ? 'check' : 'alert'" class="size-3.5" />
                        {{ $product->is_in_stock ? __('storefront.common.in_stock') : __('storefront.common.out_of_stock') }}
                    </span>
                </div>

                @if ($product->is_in_stock && $whatsappConfigured)
                    <div
                        id="order"
                        class="mt-6 scroll-mt-28 rounded-2xl border border-line bg-white p-5 shadow-panel sm:p-6"
                        x-data="orderForm(@js($orderConfig))"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-base font-extrabold">{{ __('storefront.product.select_options_title') }}</h2>
                                @if ($product->usesVariants())
                                    <p class="mt-1 text-[11px] text-muted">{{ __('storefront.product.options_required') }}</p>
                                @endif
                            </div>
                            <span class="badge-success shrink-0">{{ __('storefront.common.in_stock') }}</span>
                        </div>

                        @if ($product->usesVariants())
                            <fieldset class="mt-5" x-show="sizes.length" x-cloak>
                                <legend class="flex w-full items-center justify-between gap-2">
                                    <span class="text-xs font-bold">{{ __('storefront.product.choose_size') }}</span>
                                    <span class="text-[10px] font-semibold text-danger">{{ __('storefront.product.required_marker') }}</span>
                                </legend>
                                <div class="mt-2.5 flex flex-wrap gap-2">
                                    <template x-for="sizeOption in sizes" :key="sizeOption">
                                        <button
                                            type="button"
                                            class="inline-flex min-h-10 min-w-12 items-center justify-center rounded-lg border px-3 text-xs font-bold transition"
                                            :class="{
                                                'border-ink bg-ink text-white': size === sizeOption,
                                                'border-ink/15 bg-white text-ink hover:border-ink': size !== sizeOption && sizeAvailable(sizeOption),
                                                'cursor-not-allowed border-line bg-smoke text-muted/60 line-through': !sizeAvailable(sizeOption)
                                            }"
                                            @click="chooseSize(sizeOption)"
                                            :disabled="!sizeAvailable(sizeOption)"
                                            :aria-pressed="(size === sizeOption).toString()"
                                            :aria-label="sizeLabel(sizeOption)"
                                            :title="!sizeAvailable(sizeOption) ? messages.unavailableOption : null"
                                            x-text="sizeOption"
                                        ></button>
                                    </template>
                                </div>
                            </fieldset>

                            <fieldset class="mt-5" x-show="colors.length" x-cloak>
                                <legend class="flex w-full items-center justify-between gap-2">
                                    <span class="text-xs font-bold">{{ __('storefront.product.choose_color') }}</span>
                                    <span class="text-[10px] font-semibold text-danger">{{ __('storefront.product.required_marker') }}</span>
                                </legend>
                                <div class="mt-2.5 flex flex-wrap gap-2">
                                    <template x-for="colorOption in colors" :key="colorOption.name">
                                        <button
                                            type="button"
                                            class="inline-flex min-h-10 items-center gap-2 rounded-full border px-3 text-xs font-bold transition"
                                            :class="{
                                                'border-ink bg-ink text-white': color === colorOption.name,
                                                'border-ink/15 bg-white text-ink hover:border-ink': color !== colorOption.name && colorAvailable(colorOption.name),
                                                'cursor-not-allowed border-line bg-smoke text-muted/60 line-through': !colorAvailable(colorOption.name)
                                            }"
                                            @click="chooseColor(colorOption.name)"
                                            :disabled="!colorAvailable(colorOption.name)"
                                            :aria-pressed="(color === colorOption.name).toString()"
                                            :aria-label="colorLabel(colorOption.name)"
                                            :title="!colorAvailable(colorOption.name) ? messages.unavailableOption : colorOption.name"
                                        >
                                            <span class="size-4 rounded-full border border-ink/20" :style="'background-color: ' + (colorOption.hex || '#d4d4d0')" aria-hidden="true"></span>
                                            <span x-text="colorOption.name"></span>
                                            <x-store.icon name="check" class="size-3.5" x-show="color === colorOption.name" />
                                        </button>
                                    </template>
                                </div>
                            </fieldset>

                            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-muted" x-show="selectionComplete" x-cloak>
                                <span>
                                    {{ __('storefront.product.selected_label') }}:
                                    <strong class="font-bold text-ink" x-text="[size, color].filter(Boolean).join(' · ')"></strong>
                                </span>
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                            <span class="text-xs font-bold">{{ __('storefront.product.quantity_label') }}</span>
                            <div class="inline-flex items-center rounded-full border border-line bg-white">
                                <button
                                    type="button"
                                    class="inline-flex size-10 items-center justify-center rounded-full text-ink transition hover:bg-smoke disabled:cursor-not-allowed disabled:text-muted/30"
                                    @click="decrement()"
                                    :disabled="quantity <= 1"
                                    aria-label="{{ __('storefront.aria.quantity_decrease') }}"
                                >
                                    <x-store.icon name="minus" class="size-4" />
                                </button>
                                <span class="w-10 text-center text-sm font-extrabold tabular-nums" x-text="quantity">1</span>
                                <button
                                    type="button"
                                    class="inline-flex size-10 items-center justify-center rounded-full text-ink transition hover:bg-smoke disabled:cursor-not-allowed disabled:text-muted/30"
                                    @click="increment()"
                                    :disabled="quantity >= maxQuantity"
                                    aria-label="{{ __('storefront.aria.quantity_increase') }}"
                                >
                                    <x-store.icon name="plus" class="size-4" />
                                </button>
                            </div>
                        </div>

                        <p class="mt-2 text-[11px] font-semibold" :class="availableStock > 0 ? 'text-success' : 'text-danger'" x-text="stockText" x-show="stockText"></p>
                        <p class="mt-1.5 text-[10px] text-muted" x-show="selectionComplete && maxQuantity > 0" x-cloak x-text="maxHintText"></p>

                        <p class="mt-3 rounded-lg bg-danger-soft px-3 py-2 text-xs font-semibold text-danger" x-show="error" x-cloak role="alert" x-text="error"></p>

                        <form method="POST" action="{{ $orderUrl }}" @submit.prevent="submit">
                            @csrf
                            <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                            <input type="hidden" name="quantity" :value="quantity" value="1">

                            <button
                                type="submit"
                                class="btn-primary btn-lg mt-4 w-full"
                                :class="canOrder ? '' : 'opacity-60'"
                                :aria-disabled="(! canOrder).toString()"
                                :aria-busy="submitting.toString()"
                            >
                                <span x-show="!submitting" class="inline-flex items-center gap-2">
                                    <x-store.icon name="whatsapp" class="size-5 text-volt" />
                                    {{ __('storefront.product.order_whatsapp') }}
                                </span>
                                <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                                    <span class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>
                                    {{ __('storefront.common.loading') }}
                                </span>
                            </button>
                        </form>

                        <p class="mt-3 text-[11px] leading-5 text-muted">{{ __('storefront.product.order_note') }}</p>
                        <p class="mt-1 text-[10px] leading-5 text-muted">{{ __('storefront.product.inventory_notice') }}</p>
                    </div>
                @elseif (! $product->is_in_stock)
                    <div class="mt-6 rounded-2xl border border-line bg-smoke p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-danger-soft text-danger">
                                <x-store.icon name="alert" class="size-5" />
                            </span>
                            <div>
                                <h2 class="text-base font-extrabold">{{ __('storefront.product.stock_out') }}</h2>
                                <p class="mt-1 text-xs leading-6 text-muted">{{ __('storefront.product.out_of_stock_note') }}</p>
                                @if ($whatsappUrl)
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-outline btn-sm mt-4">
                                        <x-store.icon name="whatsapp" class="size-4" />
                                        {{ __('storefront.product.notify_whatsapp') }}
                                    </a>
                                @else
                                    <a href="{{ route('contact') }}" class="btn-outline btn-sm mt-4">{{ __('storefront.nav.contact') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-line bg-smoke p-5 sm:p-6">
                        <p class="text-sm font-bold">{{ __('storefront.product.whatsapp_unavailable') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('contact') }}" class="btn-primary btn-sm">{{ __('storefront.nav.contact') }}</a>
                            @if (filled(store_setting('contact_phone')))
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', store_setting('contact_phone')) }}" class="btn-outline btn-sm" dir="ltr">{{ store_setting('contact_phone') }}</a>
                            @endif
                        </div>
                    </div>
                @endif

                <ul class="mt-5 grid gap-2 text-[11px] leading-5 text-muted sm:grid-cols-3">
                    <li class="flex items-start gap-2">
                        <x-store.icon name="shield" class="mt-0.5 size-4 shrink-0 text-ink" />
                        {{ __('storefront.home.feature_confirm_title') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <x-store.icon name="bag" class="mt-0.5 size-4 shrink-0 text-ink" />
                        {{ __('storefront.home.feature_payment_title') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <x-store.icon name="ruler" class="mt-0.5 size-4 shrink-0 text-ink" />
                        {{ __('storefront.home.feature_sizing_title') }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-14 border-t border-line pt-10 sm:mt-20">
            <div class="grid gap-10 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)] lg:gap-14">
                <div class="space-y-10">
                    <section aria-labelledby="product-description">
                        <h2 id="product-description" class="text-xl font-extrabold">{{ __('storefront.product.description_title') }}</h2>
                        <div class="prose-ar mt-4">
                            {!! nl2br(e($product->description ?: $product->short_description)) !!}
                        </div>
                    </section>

                    @php $specificationRows = $product->specificationRows(); @endphp
                    @if ($specificationRows !== [])
                        <section aria-labelledby="product-specifications">
                            <h2 id="product-specifications" class="text-xl font-extrabold">{{ __('storefront.product.specifications_title') }}</h2>
                            <dl class="mt-4 overflow-hidden rounded-xl border border-line">
                                @foreach ($specificationRows as $specification)
                                    <div class="grid grid-cols-1 gap-1 border-b border-line px-4 py-3 text-sm last:border-0 sm:grid-cols-[150px_1fr] sm:gap-4">
                                        <dt class="font-bold text-muted">{{ $specification['key'] }}</dt>
                                        <dd class="text-ink">{{ $specification['value'] }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </section>
                    @endif
                </div>

                <aside class="space-y-4">
                    <div class="surface-card p-5">
                        <h2 class="flex items-center gap-2 text-sm font-extrabold">
                            <x-store.icon name="truck" class="size-4" />
                            {{ __('storefront.product.shipping_title') }}
                        </h2>
                        <p class="mt-2 text-xs leading-6 text-muted">{{ __('storefront.product.shipping_text') }}</p>
                    </div>

                    <div class="surface-card p-5">
                        <h2 class="flex items-center gap-2 text-sm font-extrabold">
                            <x-store.icon name="shield" class="size-4" />
                            {{ __('storefront.product.returns_title') }}
                        </h2>
                        <p class="mt-2 text-xs leading-6 text-muted">{{ __('storefront.product.returns_text') }}</p>
                        <a href="{{ route('pages.show', 'returns-exchange') }}" class="mt-2 inline-flex text-xs font-bold text-ink underline decoration-ink/25 underline-offset-4 hover:decoration-ink">
                            {{ __('storefront.footer.policies_title') }}
                        </a>
                    </div>

                    <div class="surface-card p-5">
                        <h2 class="flex items-center gap-2 text-sm font-extrabold">
                            <x-store.icon name="ruler" class="size-4" />
                            {{ __('storefront.product.size_guide') }}
                        </h2>
                        <p class="mt-2 text-xs leading-6 text-muted">{{ __('storefront.product.size_guide_short') }}</p>
                        @if ($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-ink underline decoration-ink/25 underline-offset-4 hover:decoration-ink">
                                <x-store.icon name="whatsapp" class="size-3.5" />
                                {{ __('storefront.product.notify_whatsapp') }}
                            </a>
                        @endif
                    </div>

                    <div class="surface-card p-5">
                        <h2 class="text-sm font-extrabold">{{ __('storefront.product.share_title') }}</h2>
                        <div class="mt-3 flex flex-wrap gap-2" x-data="copyLink(@js([
                            'text' => $product->url,
                            'copiedLabel' => __('storefront.product.copied'),
                            'defaultLabel' => __('storefront.product.copy_link'),
                        ]))">
                            <a href="{{ $whatsappShare }}" target="_blank" rel="noopener noreferrer" class="chip py-2! text-[11px]">
                                <x-store.icon name="whatsapp" class="size-3.5" />
                                {{ __('storefront.product.share_whatsapp') }}
                            </a>
                            <a href="{{ $xShare }}" target="_blank" rel="noopener noreferrer" class="chip py-2! text-[11px]">
                                <x-store.icon name="x-social" class="size-3.5" />
                                {{ __('storefront.product.share_x') }}
                            </a>
                            <button type="button" class="chip py-2! text-[11px]" @click="copy()">
                                <x-store.icon name="link" class="size-3.5" />
                                <span x-text="copied ? copiedLabel : defaultLabel">{{ __('storefront.product.copy_link') }}</span>
                            </button>
                            <span class="sr-only" aria-live="polite" x-text="copied ? copiedLabel : ''"></span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="container-page section-space border-t border-line mt-14 sm:mt-20">
            <x-store.section-heading
                :eyebrow="__('storefront.product.related_eyebrow')"
                :title="__('storefront.product.related_title')"
                :action-url="route('products.index')"
                :action-label="__('storefront.common.view_all')"
            />

            <div class="mt-10 grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-4">
                @foreach ($relatedProducts as $related)
                    <x-store.product-card :product="$related" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.storefront>
