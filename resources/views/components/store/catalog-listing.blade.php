@props(['products', 'filters', 'options', 'category' => null])

@php
    $showCategory = $category === null;
    $action = $category !== null ? route('categories.show', $category) : route('products.index');
    $sort = $filters['sort'] ?? 'newest';

    $hasFilters = collect([
        $filters['q'] ?? null,
        $showCategory ? ($filters['category'] ?? null) : null,
        $filters['min_price'] ?? null,
        $filters['max_price'] ?? null,
        $filters['sizes'] ?? null,
        $filters['colors'] ?? null,
        $filters['availability'] ?? null,
        $filters['featured'] ?? null,
        $filters['on_sale'] ?? null,
    ])->contains(fn ($value) => filled($value));

    $activeFilterCount = collect([
        $filters['q'] ?? null,
        $showCategory ? ($filters['category'] ?? null) : null,
        ($filters['min_price'] ?? null) !== null || ($filters['max_price'] ?? null) !== null ? 'price' : null,
        $filters['sizes'] ?? null,
        $filters['colors'] ?? null,
        $filters['availability'] ?? null,
        $filters['featured'] ?? null,
        $filters['on_sale'] ?? null,
    ])->filter(fn ($value) => filled($value))->count()
        + max(0, count((array) ($filters['sizes'] ?? [])) - 1)
        + max(0, count((array) ($filters['colors'] ?? [])) - 1);

    $categoryOptions = collect($options['categories'])
        ->flatMap(fn ($root) => collect([$root])->concat($root->children));
    $activeCategory = $showCategory && filled($filters['category'] ?? null)
        ? $categoryOptions->firstWhere('slug', $filters['category'])
        : null;

    $chips = [];

    if (filled($filters['q'] ?? null)) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_search', ['value' => $filters['q']]),
            'url' => catalog_filter_url('q'),
        ];
    }

    if ($activeCategory) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_category', ['value' => $activeCategory->name]),
            'url' => catalog_filter_url('category'),
        ];
    }

    if (($filters['min_price'] ?? null) !== null) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_price_min', ['min' => money($filters['min_price'])]),
            'url' => catalog_filter_url('min_price'),
        ];
    }

    if (($filters['max_price'] ?? null) !== null) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_price_max', ['max' => money($filters['max_price'])]),
            'url' => catalog_filter_url('max_price'),
        ];
    }

    foreach ((array) ($filters['sizes'] ?? []) as $size) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_size', ['value' => $size]),
            'url' => catalog_filter_url('sizes', (string) $size),
        ];
    }

    foreach ((array) ($filters['colors'] ?? []) as $color) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_color', ['value' => $color]),
            'url' => catalog_filter_url('colors', (string) $color),
        ];
    }

    if (filled($filters['availability'] ?? null)) {
        $chips[] = [
            'label' => __('storefront.catalog.active_filter_availability', [
                'value' => $filters['availability'] === 'in_stock'
                    ? __('storefront.catalog.in_stock')
                    : __('storefront.catalog.out_of_stock'),
            ]),
            'url' => catalog_filter_url('availability'),
        ];
    }

    if (! empty($filters['featured'])) {
        $chips[] = ['label' => __('storefront.catalog.active_filter_featured'), 'url' => catalog_filter_url('featured')];
    }

    if (! empty($filters['on_sale'])) {
        $chips[] = ['label' => __('storefront.catalog.active_filter_sale'), 'url' => catalog_filter_url('on_sale')];
    }
@endphp

<div x-data="catalogFilters()">
    <div class="lg:flex lg:items-start lg:gap-10">
        <aside class="hidden w-[260px] shrink-0 lg:block">
            <div class="sticky top-28 rounded-2xl border border-line bg-white p-5">
                <div class="mb-5 flex items-center justify-between gap-2 border-b border-line pb-4">
                    <h2 class="text-sm font-extrabold">{{ __('storefront.catalog.filters_title') }}</h2>
                    @if ($activeFilterCount > 0)
                        <span class="badge-muted">{{ $activeFilterCount }}</span>
                    @endif
                </div>
                @include('storefront.partials.filters', [
                    'context' => 'desktop',
                    'action' => $action,
                    'showCategory' => $showCategory,
                ])
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs font-semibold text-muted">
                    @if ($products->total() > 0)
                        {{ __('storefront.catalog.showing', ['from' => $products->firstItem(), 'to' => $products->lastItem(), 'total' => $products->total()]) }}
                    @else
                        {{ __('storefront.catalog.results_total', ['total' => 0]) }}
                    @endif
                </p>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        class="btn-outline btn-sm lg:hidden"
                        @click="open()"
                        aria-label="{{ __('storefront.aria.filters_open') }}"
                    >
                        <x-store.icon name="filter" class="size-4" />
                        {{ __('storefront.catalog.filters_button') }}
                        @if ($activeFilterCount > 0)
                            <span class="flex size-5 items-center justify-center rounded-full bg-ink text-[10px] text-white">{{ $activeFilterCount }}</span>
                        @endif
                    </button>

                    <form method="GET" action="{{ $action }}" class="flex items-center gap-2">
                        @if (filled($filters['q'] ?? null))
                            <input type="hidden" name="q" value="{{ $filters['q'] }}">
                        @endif
                        @if ($showCategory && filled($filters['category'] ?? null))
                            <input type="hidden" name="category" value="{{ $filters['category'] }}">
                        @endif
                        @foreach (['min_price', 'max_price', 'availability'] as $key)
                            @if (filled($filters[$key] ?? null))
                                <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}">
                            @endif
                        @endforeach
                        @foreach ((array) ($filters['sizes'] ?? []) as $size)
                            <input type="hidden" name="sizes[]" value="{{ $size }}">
                        @endforeach
                        @foreach ((array) ($filters['colors'] ?? []) as $color)
                            <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach
                        @if (! empty($filters['featured']))
                            <input type="hidden" name="featured" value="1">
                        @endif
                        @if (! empty($filters['on_sale']))
                            <input type="hidden" name="on_sale" value="1">
                        @endif

                        <label for="catalog-sort" class="sr-only">{{ __('storefront.aria.sort_results') }}</label>
                        <select
                            id="catalog-sort"
                            name="sort"
                            class="field h-9 min-w-[150px] py-0 text-[11px] font-semibold"
                            onchange="this.form.submit()"
                        >
                            <option value="newest" @selected($sort === 'newest')>{{ __('storefront.catalog.sort_newest') }}</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>{{ __('storefront.catalog.sort_price_asc') }}</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>{{ __('storefront.catalog.sort_price_desc') }}</option>
                        </select>
                        <noscript>
                            <button type="submit" class="btn-outline btn-sm">{{ __('storefront.common.apply') }}</button>
                        </noscript>
                    </form>
                </div>
            </div>

            @if ($chips !== [])
                <div class="mt-4 flex flex-wrap items-center gap-2 rounded-xl bg-smoke/70 p-3">
                    <span class="text-[10px] font-bold text-muted">{{ __('storefront.catalog.active_filters') }}:</span>
                    @foreach ($chips as $chip)
                        <a
                            href="{{ $chip['url'] }}"
                            class="inline-flex items-center gap-1.5 rounded-full border border-ink/10 bg-white px-2.5 py-1 text-[10px] font-semibold text-ink transition hover:border-ink/30"
                            aria-label="{{ __('storefront.catalog.remove_filter') }}: {{ $chip['label'] }}"
                        >
                            {{ $chip['label'] }}
                            <x-store.icon name="close" class="size-3 text-muted" />
                        </a>
                    @endforeach
                    <a href="{{ url()->current() }}" class="ms-auto text-[10px] font-bold text-ink underline decoration-ink/25 underline-offset-4 hover:decoration-ink">
                        {{ __('storefront.common.clear_all') }}
                    </a>
                </div>
            @endif

            @if ($products->isNotEmpty())
                <div class="mt-7 grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                        <x-store.product-card :product="$product" :eager="$loop->first" />
                    @endforeach
                </div>

                {{ $products->links() }}
            @else
                <div class="mt-7 flex flex-col items-center justify-center rounded-2xl border border-dashed border-line bg-smoke/40 px-6 py-16 text-center">
                    <span class="flex size-14 items-center justify-center rounded-full bg-white text-muted shadow-sm">
                        <x-store.icon name="search" class="size-6" />
                    </span>
                    <h2 class="mt-5 text-xl font-extrabold">
                        {{ $hasFilters ? __('storefront.catalog.no_results_title') : __('storefront.catalog.no_products_title') }}
                    </h2>
                    <p class="mt-2 max-w-md text-sm leading-7 text-muted">
                        {{ $hasFilters ? __('storefront.catalog.no_results_text') : __('storefront.catalog.no_products_text') }}
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ url()->current() }}" class="btn-primary btn-sm">{{ __('storefront.catalog.clear_filters') }}</a>
                        @endif
                        <a href="{{ route('products.index') }}" class="btn-outline btn-sm">
                            {{ __('storefront.catalog.all_products') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div
        x-show="filtersOpen"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('storefront.catalog.filters_title') }}"
        @keydown.escape.window="close()"
        @keydown.tab="trapFocus"
    >
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="close()"></div>
        <div x-ref="filterPanel" class="absolute inset-y-0 start-0 w-[92%] max-w-sm overflow-y-auto overscroll-contain bg-paper p-5 shadow-lift">
            @include('storefront.partials.filters', [
                'context' => 'mobile',
                'action' => $action,
                'showCategory' => $showCategory,
            ])
        </div>
    </div>
</div>
