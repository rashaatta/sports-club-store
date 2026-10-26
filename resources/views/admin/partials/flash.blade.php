@if (session('status'))
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        class="mb-5 flex items-start gap-3 rounded-xl border border-success/20 bg-success-soft px-4 py-3 text-sm font-semibold text-success"
        role="status"
    >
        <x-store.icon name="check" class="mt-0.5 size-4 shrink-0" />
        <p class="flex-1">{{ session('status') }}</p>
        <button type="button" class="rounded-full p-1 hover:bg-success/10" @click="visible = false" aria-label="{{ __('admin.common.close') }}">
            <x-store.icon name="close" class="size-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        class="mb-5 flex items-start gap-3 rounded-xl border border-danger/20 bg-danger-soft px-4 py-3 text-sm font-semibold text-danger"
        role="alert"
    >
        <x-store.icon name="alert" class="mt-0.5 size-4 shrink-0" />
        <p class="flex-1">{{ session('error') }}</p>
        <button type="button" class="rounded-full p-1 hover:bg-danger/10" @click="visible = false" aria-label="{{ __('admin.common.close') }}">
            <x-store.icon name="close" class="size-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-xl border border-danger/20 bg-danger-soft px-4 py-3 text-sm text-danger" role="alert">
        <p class="font-bold">{{ __('storefront.common.error_summary') }}</p>
        <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs font-semibold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
