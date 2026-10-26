@php
    $context = $context ?? 'desktop';
    $selectedSizes = array_map('strval', (array) ($filters['sizes'] ?? []));
    $selectedColors = array_map('strval', (array) ($filters['colors'] ?? []));
    $selectedAvailability = $filters['availability'] ?? '';
    $selectedCategory = $filters['category'] ?? '';
    $selectedMin = $filters['min_price'] ?? null;
    $selectedMax = $filters['max_price'] ?? null;
    $inputId = fn (string $name) => 'filter-'.$context.'-'.$name;
@endphp

<form method="GET" action="{{ $action }}" class="space-y-6">
    <div class="flex items-center justify-between gap-3 border-b border-line pb-4 lg:hidden">
        <div>
            <p class="text-base font-extrabold">{{ __('storefront.catalog.filters_title') }}</p>
            <p class="mt-0.5 text-[11px] text-muted">{{ __('storefront.catalog.results_total', ['total' => $products->total()]) }}</p>
        </div>
        <button
            type="button"
            class="icon-btn border border-line hover:bg-smoke"
            @click="close()"
            aria-label="{{ __('storefront.catalog.filters_close') }}"
        >
            <x-store.icon name="close" class="size-5" />
        </button>
    </div>

    <div>
        <label for="{{ $inputId('q') }}" class="field-label">{{ __('storefront.common.search') }}</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                <x-store.icon name="search" class="size-4" />
            </span>
            <input
                id="{{ $inputId('q') }}"
                type="search"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="{{ __('storefront.catalog.search_placeholder') }}"
                class="field h-11 ps-9 text-xs"
            >
        </div>
    </div>

    @if ($showCategory)
        <div>
            <label for="{{ $inputId('category') }}" class="field-label">{{ __('storefront.catalog.category') }}</label>
            <select id="{{ $inputId('category') }}" name="category" class="field h-11 text-xs">
                <option value="">{{ __('storefront.catalog.all_categories') }}</option>
                @foreach ($options['categories'] as $rootCategory)
                    <option value="{{ $rootCategory->slug }}" @selected($selectedCategory === $rootCategory->slug)>{{ $rootCategory->name }}</option>
                    @foreach ($rootCategory->children as $childCategory)
                        <option value="{{ $childCategory->slug }}" @selected($selectedCategory === $childCategory->slug)>— {{ $childCategory->name }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
    @endif

    <fieldset>
        <legend class="field-label">{{ __('storefront.catalog.price_range') }}</legend>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label for="{{ $inputId('min_price') }}" class="sr-only">{{ __('storefront.catalog.price_min') }}</label>
                <div class="relative">
                    <input
                        id="{{ $inputId('min_price') }}"
                        type="number"
                        name="min_price"
                        inputmode="numeric"
                        min="0"
                        max="1000000"
                        value="{{ $selectedMin }}"
                        placeholder="{{ $options['min_price'] }}"
                        class="field h-11 pe-12 text-xs tabular-nums"
                    >
                    <span class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-[10px] text-muted">{{ store_setting('currency_symbol') }}</span>
                </div>
                <span class="mt-1 block text-[10px] text-muted">{{ __('storefront.catalog.price_min') }}</span>
            </div>
            <div>
                <label for="{{ $inputId('max_price') }}" class="sr-only">{{ __('storefront.catalog.price_max') }}</label>
                <div class="relative">
                    <input
                        id="{{ $inputId('max_price') }}"
                        type="number"
                        name="max_price"
                        inputmode="numeric"
                        min="0"
                        max="1000000"
                        value="{{ $selectedMax }}"
                        placeholder="{{ $options['max_price'] }}"
                        class="field h-11 pe-12 text-xs tabular-nums"
                    >
                    <span class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-[10px] text-muted">{{ store_setting('currency_symbol') }}</span>
                </div>
                <span class="mt-1 block text-[10px] text-muted">{{ __('storefront.catalog.price_max') }}</span>
            </div>
        </div>
    </fieldset>

    @if ($options['sizes']->isNotEmpty())
        <fieldset>
            <legend class="field-label">{{ __('storefront.catalog.sizes') }}</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($options['sizes'] as $size)
                    @php $sizeId = $inputId('size-'.$loop->index); @endphp
                    <input
                        id="{{ $sizeId }}"
                        type="checkbox"
                        name="sizes[]"
                        value="{{ $size }}"
                        class="peer sr-only"
                        @checked(in_array((string) $size, $selectedSizes, true))
                    >
                    <label
                        for="{{ $sizeId }}"
                        class="chip min-w-11 cursor-pointer justify-center px-3 py-1.5 text-[11px] peer-checked:border-ink peer-checked:bg-ink peer-checked:text-white peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2"
                        aria-label="{{ __('storefront.aria.size_filter', ['size' => $size]) }}"
                    >{{ $size }}</label>
                @endforeach
            </div>
        </fieldset>
    @endif

    @if ($options['colors']->isNotEmpty())
        <fieldset>
            <legend class="field-label">{{ __('storefront.catalog.colors') }}</legend>
            <div class="space-y-2">
                @foreach ($options['colors'] as $color)
                    @php
                        $colorId = $inputId('color-'.$loop->index);
                        $hex = preg_match('/^#[0-9a-f]{3,8}$/i', (string) $color['hex']) ? $color['hex'] : '#d4d4d0';
                    @endphp
                    <input
                        id="{{ $colorId }}"
                        type="checkbox"
                        name="colors[]"
                        value="{{ $color['name'] }}"
                        class="peer sr-only"
                        @checked(in_array($color['name'], $selectedColors, true))
                    >
                    <label
                        for="{{ $colorId }}"
                        class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-transparent px-2 py-1.5 text-xs font-semibold text-ink/75 transition hover:bg-smoke peer-checked:border-ink/20 peer-checked:bg-smoke peer-checked:text-ink peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2"
                        aria-label="{{ __('storefront.aria.color_filter', ['name' => $color['name']]) }}"
                    >
                        <span class="size-4 rounded-full border border-ink/15" style="background-color: {{ $hex }}" aria-hidden="true"></span>
                        {{ $color['name'] }}
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endif

    <fieldset>
        <legend class="field-label">{{ __('storefront.catalog.availability') }}</legend>
        <div class="space-y-2.5">
            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-ink/75">
                <input type="radio" name="availability" value="" class="size-4 border-ink/25 text-ink focus:ring-0" @checked($selectedAvailability === '')>
                {{ __('storefront.catalog.any_availability') }}
            </label>
            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-ink/75">
                <input type="radio" name="availability" value="in_stock" class="size-4 border-ink/25 text-ink focus:ring-0" @checked($selectedAvailability === 'in_stock')>
                {{ __('storefront.catalog.in_stock') }}
            </label>
            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-ink/75">
                <input type="radio" name="availability" value="out_of_stock" class="size-4 border-ink/25 text-ink focus:ring-0" @checked($selectedAvailability === 'out_of_stock')>
                {{ __('storefront.catalog.out_of_stock') }}
            </label>
        </div>
    </fieldset>

    <fieldset class="border-t border-line pt-5">
        <legend class="sr-only">{{ __('storefront.common.filters') }}</legend>
        <div class="space-y-3">
            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-ink/75">
                <input type="checkbox" name="featured" value="1" class="size-4 rounded border-ink/25 text-ink focus:ring-0" @checked(request()->boolean('featured'))>
                {{ __('storefront.catalog.featured_only') }}
            </label>
            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-ink/75">
                <input type="checkbox" name="on_sale" value="1" class="size-4 rounded border-ink/25 text-ink focus:ring-0" @checked(request()->boolean('on_sale'))>
                {{ __('storefront.catalog.on_sale_only') }}
            </label>
        </div>
    </fieldset>

    @if (filled($filters['sort'] ?? null))
        <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
    @endif

    <div class="space-y-2 pt-1">
        <button type="submit" class="btn-primary w-full">{{ __('storefront.catalog.filters_apply') }}</button>
        <a href="{{ url()->current() }}" class="btn-ghost btn-sm w-full text-muted hover:text-ink">
            {{ __('storefront.catalog.clear_filters') }}
        </a>
    </div>
</form>
