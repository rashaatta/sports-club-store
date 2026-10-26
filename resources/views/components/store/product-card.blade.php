@props(['product', 'eager' => false])

@php
    $image = $product->images->first();
    $discount = $product->discount_percent;
    $usesVariants = $product->usesVariants();
    $whatsappConfigured = app(\App\Services\WhatsAppService::class)->isConfigured();
    $sizesCount = $usesVariants ? $product->variants->where('is_active', true)->pluck('size')->filter()->unique()->count() : 0;
    $stock = $product->total_stock;
@endphp

<article class="product-card group relative flex flex-col">
    <div class="relative">
        <a
            href="{{ $product->url }}"
            class="product-media block aspect-[4/5] rounded-xl"
            aria-label="{{ $product->name }}"
        >
            @if ($image)
                <img
                    src="{{ media_url($image->path) }}"
                    @if (media_srcset($image->path)) srcset="{{ media_srcset($image->path) }}" sizes="(min-width: 1280px) 22vw, (min-width: 768px) 33vw, 50vw" @endif
                    alt="{{ $image->alt_text ?: __('storefront.product.image_alt', ['name' => $product->name]) }}"
                    width="800"
                    height="1000"
                    loading="{{ $eager ? 'eager' : 'lazy' }}"
                    @if ($eager) fetchpriority="high" @endif
                >
            @else
                <span class="flex size-full flex-col items-center justify-center gap-2 text-muted">
                    <x-store.icon name="image" class="size-8" />
                    <span class="text-[11px]">{{ __('storefront.product.no_image') }}</span>
                </span>
            @endif
        </a>

        <div class="pointer-events-none absolute inset-x-2.5 top-2.5 flex flex-wrap items-start gap-1.5">
            @if ($discount)
                <span class="badge-volt">{{ __('storefront.common.discount', ['percent' => $discount]) }}</span>
            @endif
            @if ($product->is_new_arrival)
                <span class="badge-ink">{{ __('storefront.common.new') }}</span>
            @endif
            @if (! $product->is_in_stock)
                <span class="badge-muted bg-white/90">{{ __('storefront.common.out_of_stock') }}</span>
            @endif
        </div>

        <div class="absolute inset-x-2.5 bottom-2.5">
            @if ($product->is_in_stock && $whatsappConfigured)
                @if ($usesVariants)
                    <a
                        href="{{ $product->url }}#order"
                        class="quick-action flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-white/95 px-3 text-xs font-bold text-ink shadow-sm backdrop-blur transition hover:bg-ink hover:text-white"
                        aria-label="{{ __('storefront.product.choose_options') }} — {{ $product->name }}"
                    >
                        <x-store.icon name="ruler" class="size-4" />
                        {{ __('storefront.product.choose_options') }}
                    </a>
                @else
                    <form method="POST" action="{{ route('products.order', $product) }}" target="_blank" class="quick-action">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button
                            type="submit"
                            class="flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-white/95 px-3 text-xs font-bold text-ink shadow-sm backdrop-blur transition hover:bg-ink hover:text-white"
                            aria-label="{{ __('storefront.aria.quick_order', ['name' => $product->name]) }}"
                        >
                            <x-store.icon name="whatsapp" class="size-4" />
                            {{ __('storefront.product.add_quick') }}
                        </button>
                    </form>
                @endif
            @else
                <a
                    href="{{ $product->url }}"
                    class="quick-action flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-white/95 px-3 text-xs font-bold text-ink shadow-sm backdrop-blur transition hover:bg-ink hover:text-white"
                >
                    {{ __('storefront.product.view_details') }}
                </a>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col pt-3.5">
        <div class="flex items-start justify-between gap-3">
            <p class="truncate text-[10px] font-bold text-muted">{{ $product->category?->name }}</p>
            @if ($product->is_in_stock)
                @if ($product->is_low_stock)
                    <span class="shrink-0 text-[10px] font-bold text-danger">{{ __('storefront.common.low_stock') }}</span>
                @else
                    <span class="shrink-0 text-[10px] font-bold text-success">{{ __('storefront.common.in_stock') }}</span>
                @endif
            @else
                <span class="shrink-0 text-[10px] font-bold text-muted">{{ __('storefront.common.out_of_stock') }}</span>
            @endif
        </div>

        <h3 class="mt-1 text-[15px] font-extrabold leading-6 text-ink">
            <a href="{{ $product->url }}" class="transition hover:text-carbon">{{ $product->name }}</a>
        </h3>

        <div class="mt-auto flex flex-wrap items-baseline gap-x-2 gap-y-1 pt-2">
            <span class="text-base font-extrabold text-ink">{{ $product->price_formatted }}</span>
            @if ($product->compare_at_price)
                <span class="text-xs font-semibold text-muted line-through">{{ $product->compare_at_formatted }}</span>
            @endif
        </div>

        @if ($usesVariants && $sizesCount > 0)
            <p class="mt-1 text-[10px] font-semibold text-muted">{{ $sizesCount }} {{ __('storefront.common.size') }}</p>
        @endif
        @if (! $product->is_in_stock)
            <p class="mt-1 text-[10px] text-muted">{{ __('storefront.product.stock_out') }}</p>
        @endif
    </div>
</article>
