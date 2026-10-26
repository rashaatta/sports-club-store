@php
    $isSearch = filled($filters['q'] ?? null);
    $pageSchema = [];

    if ($products->isNotEmpty()) {
        $pageSchema[] = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $pageTitle,
            'url' => $canonical,
            'numberOfItems' => $products->total(),
            'itemListElement' => $products->getCollection()->values()->map(fn ($product, $index) => [
                '@type' => 'ListItem',
                'position' => $products->firstItem() + $index,
                'url' => $product->url,
                'name' => $product->name,
            ])->all(),
        ];
    }
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :robots="$robots"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-8 sm:py-10">
            <x-store.breadcrumbs :items="[
                ['label' => __('storefront.catalog.all_products'), 'url' => route('products.index')],
                ...($isSearch ? [['label' => __('storefront.search.results_title')]] : []),
            ]" />

            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow flex items-center gap-2.5">
                        <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                        {{ $isSearch ? __('storefront.catalog.search_eyebrow') : __('storefront.catalog.eyebrow') }}
                    </p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $pageTitle }}</h1>
                    <p class="mt-3 text-sm leading-7 text-muted">
                        {{ $isSearch ? __('storefront.catalog.search_hint') : $metaDescription }}
                    </p>
                </div>

                <p class="shrink-0 rounded-full border border-line bg-white px-4 py-2 text-xs font-bold text-muted">
                    {{ __('storefront.catalog.results_total', ['total' => $products->total()]) }}
                </p>
            </div>
        </div>
    </div>

    <div class="container-page py-10 sm:py-14">
        <x-store.catalog-listing
            :products="$products"
            :filters="$filters"
            :options="$options"
        />
    </div>
</x-layouts.storefront>
