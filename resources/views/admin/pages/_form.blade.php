@php($isAbout = $page?->slug === 'about-us')

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="space-y-6">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="page-content-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                    <x-store.icon name="page" class="size-4" />
                </span>
                <h2 id="page-content-title" class="text-base font-black">{{ __('admin.pages.title') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.field name="title" :label="__('admin.pages.field_title')" :value="$page?->title" maxlength="180" required />
                <x-admin.field
                    name="slug"
                    :label="__('admin.pages.field_slug')"
                    :value="$page?->slug"
                    :help="$isAbout ? __('admin.pages.about_locked') : __('admin.products.field_slug_hint')"
                    dir="ltr"
                    :disabled="$isAbout"
                />
                <div class="sm:col-span-2">
                    <x-admin.field
                        name="content"
                        type="textarea"
                        :label="__('admin.pages.field_content')"
                        :value="$page?->content"
                        :help="__('admin.pages.field_content_hint')"
                        rows="18"
                        class="[&_textarea]:font-mono [&_textarea]:text-[13px] [&_textarea]:leading-6"
                        required
                    />
                    <p class="mt-1 text-xs text-muted">{{ __('admin.pages.content_note') }}</p>
                </div>
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="page-seo-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="search" class="size-4" />
                </span>
                <h2 id="page-seo-title" class="text-base font-black">{{ __('admin.common.seo') }}</h2>
            </div>
            <div class="grid gap-4">
                <x-admin.field name="meta_title" :label="__('admin.pages.field_meta_title')" :value="$page?->meta_title" maxlength="255" />
                <x-admin.field name="meta_description" type="textarea" :label="__('admin.pages.field_meta_description')" :value="$page?->meta_description" rows="3" maxlength="500" />
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="surface-card p-5" aria-labelledby="page-status-title">
            <h2 id="page-status-title" class="text-base font-black">{{ __('admin.common.status') }}</h2>
            <div class="mt-4 space-y-3">
                <x-admin.toggle name="is_active" :label="__('admin.pages.is_active')" :checked="$page?->is_active ?? true" />
                <x-admin.toggle name="show_in_footer" :label="__('admin.pages.show_in_footer')" :checked="$page?->show_in_footer ?? false" />
            </div>
            <div class="mt-4">
                <x-admin.field
                    name="sort_order"
                    type="number"
                    :label="__('admin.common.sort_order')"
                    :value="$page?->sort_order ?? 0"
                    min="0"
                    step="1"
                    required
                />
            </div>
            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="page-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.pages.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>

        @if ($isAbout)
            <div class="rounded-2xl bg-ink p-5 text-white">
                <p class="flex items-center gap-2 text-xs font-bold text-volt">
                    <x-store.icon name="info" class="size-4" />
                    {{ __('admin.pages.about_note') }}
                </p>
            </div>
        @endif

        @if ($page?->is_active)
            <a href="{{ $isAbout ? route('about') : route('pages.show', $page) }}" target="_blank" rel="noopener" class="surface-card flex items-center justify-between gap-3 p-4 text-sm font-bold transition hover:border-ink/25">
                {{ __('admin.pages.preview') }}
                <x-store.icon name="external" class="size-4 text-muted" />
            </a>
        @endif
    </div>
</div>
