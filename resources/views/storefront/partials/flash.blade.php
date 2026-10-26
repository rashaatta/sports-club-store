@if (session('status'))
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        x-transition
        class="fixed inset-x-4 bottom-5 z-[70] mx-auto max-w-md rounded-xl border border-success/20 bg-white p-4 shadow-lift"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                <x-store.icon name="check" class="size-4" />
            </span>
            <p class="flex-1 text-sm font-semibold leading-6 text-ink">{{ session('status') }}</p>
            <button type="button" class="-me-1 rounded-full p-1 text-muted hover:bg-smoke hover:text-ink" @click="visible = false" aria-label="{{ __('storefront.common.close') }}">
                <x-store.icon name="close" class="size-4" />
            </button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        x-transition
        class="fixed inset-x-4 bottom-5 z-[70] mx-auto max-w-md rounded-xl border border-danger/20 bg-white p-4 shadow-lift"
        role="alert"
    >
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-danger-soft text-danger">
                <x-store.icon name="alert" class="size-4" />
            </span>
            <div class="flex-1">
                <p class="text-sm font-bold text-ink">{{ __('storefront.common.error_summary') }}</p>
                <ul class="mt-1 space-y-1 text-xs leading-5 text-muted">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="-me-1 rounded-full p-1 text-muted hover:bg-smoke hover:text-ink" @click="visible = false" aria-label="{{ __('storefront.common.close') }}">
                <x-store.icon name="close" class="size-4" />
            </button>
        </div>
    </div>
@endif
