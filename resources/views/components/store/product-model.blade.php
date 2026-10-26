@props(['model'])

@if (filled($model['src'] ?? null))
    <div x-data="productModel(@js($model))">
        <button type="button" class="btn-outline btn-sm mt-3 w-full" @click="show()">
            <x-store.icon name="cube" class="size-4" />
            {{ __('storefront.product.view_3d') }}
        </button>

        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[80] flex items-center justify-center bg-ink/95 p-4 sm:p-10"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('storefront.product.view_3d') }}"
            @keydown.escape.window="hide()"
            @keydown.tab="trapFocus"
            @click.self="hide()"
        >
            <button
                type="button"
                class="absolute end-4 top-4 inline-flex size-11 items-center justify-center rounded-full border border-white/20 text-white transition hover:bg-white hover:text-ink"
                @click="hide()"
                x-ref="closeButton"
                aria-label="{{ __('storefront.product.view_3d_close') }}"
            >
                <x-store.icon name="close" class="size-5" />
            </button>

            <div class="w-full max-w-4xl">
                <div
                    x-show="! ready && ! failed"
                    class="flex h-[50vh] flex-col items-center justify-center gap-3 rounded-xl border border-white/10 bg-white/5 text-white/60"
                >
                    <span class="size-6 animate-spin rounded-full border-2 border-white/25 border-t-volt" aria-hidden="true"></span>
                    <span class="text-xs font-semibold">{{ __('storefront.common.loading') }}</span>
                </div>

                <div
                    x-show="failed"
                    x-cloak
                    class="rounded-xl border border-white/10 bg-white/5 px-6 py-10 text-center"
                >
                    <x-store.icon name="alert" class="mx-auto size-8 text-white/60" />
                    <p class="mt-3 text-sm leading-7 text-white/70">{{ __('storefront.product.model_error') }}</p>
                </div>

                <template x-if="ready">
                    <model-viewer
                        :src="src"
                        :alt="alt"
                        :poster="poster || undefined"
                        camera-controls
                        touch-action="pan-y"
                        auto-rotate
                        rotation-per-second="18deg"
                        shadow-intensity="1"
                        environment-image="neutral"
                        exposure="1"
                        loading="lazy"
                        tabindex="0"
                        class="h-[70vh] w-full rounded-xl bg-white/5"
                    ></model-viewer>
                </template>

                <p class="mt-3 text-center text-[11px] text-white/50">{{ __('storefront.product.view_3d_hint') }}</p>
            </div>
        </div>
    </div>
@endif
