@php
    $bannerImage = media_url($category->image_path);
    $pageSchema = [];

    if ($products->isNotEmpty()) {
        $pageSchema[] = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $category->name,
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

    $crumbs = $breadcrumb
        ->map(fn ($item) => [
            'label' => $item->name,
            'url' => $item->is($category) ? null : route('categories.show', $item),
        ])
        ->values()
        ->all();
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :robots="$robots"
    :og-image="$bannerImage"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-6 sm:py-8">
            <x-store.breadcrumbs :items="$crumbs" />
        </div>
    </div>

    <section class="container-page py-6 sm:py-8">
        <div class="relative isolate overflow-hidden rounded-2xl bg-ink text-white">
            @if ($bannerImage)
                <img
                    src="{{ $bannerImage }}"
                    @if (media_srcset($category->image_path)) srcset="{{ media_srcset($category->image_path) }}" sizes="100vw" @endif
                    alt="{{ __('storefront.category.hero_alt', ['name' => $category->name]) }}"
                    width="1024"
                    height="1280"
                    class="absolute inset-0 size-full object-cover object-center opacity-55"
                    @if ($category->is($breadcrumb->first())) fetchpriority="high" @endif
                >
                <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/65 to-ink/20" aria-hidden="true"></div>
            @else
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,#242528_0%,#0b0b0c_60%)]" aria-hidden="true"></div>
            @endif

            <div class="relative max-w-2xl px-6 py-12 sm:px-10 sm:py-16 lg:py-20">
                <p class="eyebrow-latin text-volt">{{ __('storefront.category.eyebrow') }}</p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-5xl">{{ $category->name }}</h1>
                @if (filled($category->description))
                    <p class="mt-4 max-w-xl text-sm leading-7 text-white/70">{{ $category->description }}</p>
                @endif
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="badge border border-white/20 bg-white/10 text-white">{{ __('storefront.category.products_count', ['count' => $products->total()]) }}</span>
                    @if ($category->children->isNotEmpty())
                        <span class="text-[11px] font-semibold text-white/50">{{ __('storefront.category.subcategories') }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if ($category->children->isNotEmpty())
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($category->children as $child)
                    <a href="{{ route('categories.show', $child) }}" class="chip hover:bg-ink hover:text-white">
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <div class="container-page pb-14 sm:pb-20">
        <x-store.catalog-listing
            :products="$products"
            :filters="$filters"
            :options="$options"
            :category="$category"
        />
    </div>
</x-layouts.storefront>
