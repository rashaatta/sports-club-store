<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="space-y-6">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="banner-details-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                    <x-store.icon name="image" class="size-4" />
                </span>
                <h2 id="banner-details-title" class="text-base font-black">{{ __('admin.banners.title') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.field name="title" :label="__('admin.banners.field_title')" :value="$banner?->title" maxlength="180" required />
                <x-admin.field name="subtitle" :label="__('admin.banners.field_subtitle')" :value="$banner?->subtitle" maxlength="500" />
                <x-admin.field
                    name="link_url"
                    :label="__('admin.banners.field_link')"
                    :value="$banner?->link_url"
                    :help="__('admin.banners.field_link_hint')"
                    maxlength="500"
                    dir="ltr"
                />
                <x-admin.field name="button_label" :label="__('admin.banners.field_button')" :value="$banner?->button_label" maxlength="60" />
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="banner-schedule-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="calendar" class="size-4" />
                </span>
                <h2 id="banner-schedule-title" class="text-base font-black">{{ __('admin.banners.table_schedule') }}</h2>
            </div>
            <p class="mb-4 text-xs text-muted">{{ __('admin.banners.schedule_hint') }}</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.field
                    name="starts_at"
                    type="datetime-local"
                    :label="__('admin.banners.field_starts_at')"
                    :value="$banner?->starts_at?->format('Y-m-d\TH:i')"
                />
                <x-admin.field
                    name="ends_at"
                    type="datetime-local"
                    :label="__('admin.banners.field_ends_at')"
                    :value="$banner?->ends_at?->format('Y-m-d\TH:i')"
                />
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="surface-card p-5" aria-labelledby="banner-status-title">
            <h2 id="banner-status-title" class="text-base font-black">{{ __('admin.common.status') }}</h2>
            <div class="mt-4">
                <x-admin.toggle name="is_active" :label="__('admin.banners.is_active')" :checked="$banner?->is_active ?? true" />
            </div>
            <div class="mt-4">
                <x-admin.select
                    name="placement"
                    :label="__('admin.banners.field_placement')"
                    :options="['home_promo' => __('admin.banners.placement_home_promo')]"
                    :selected="$banner?->placement ?? 'home_promo'"
                    required
                />
                <p class="mt-1 text-xs text-muted">{{ __('admin.banners.placement_hint') }}</p>
            </div>
            <div class="mt-4">
                <x-admin.field
                    name="sort_order"
                    type="number"
                    :label="__('admin.common.sort_order')"
                    :value="$banner?->sort_order ?? 0"
                    min="0"
                    step="1"
                    required
                />
            </div>
            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="banner-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>

        <section class="surface-card p-5" aria-labelledby="banner-image-title">
            <h2 id="banner-image-title" class="text-base font-black">{{ __('admin.banners.field_image') }}</h2>
            <p class="mt-1 text-xs leading-5 text-muted">{{ __('admin.banners.field_image_hint') }}</p>

            @if ($banner?->image_path)
                <div class="relative mt-4 aspect-[16/8] overflow-hidden rounded-xl border border-line bg-smoke">
                    <img src="{{ media_url($banner->image_path) }}" alt="" class="size-full object-cover">
                </div>
                <p class="mt-2 text-[11px] font-bold text-muted">{{ __('admin.banners.current_image') }}</p>
            @endif

            <label for="banner-image" class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-line bg-smoke/40 px-4 py-6 text-center transition hover:border-ink/30 hover:bg-smoke">
                <x-store.icon name="upload" class="size-5 text-muted" />
                <span class="mt-2 text-xs font-bold">{{ $banner?->image_path ? __('admin.banners.update_image') : __('admin.banners.field_image') }}</span>
                <span class="mt-1 text-[10px] text-muted">JPG, PNG, WebP · 800×400+</span>
                <input id="banner-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @required(! $banner)>
            </label>
            @error('image')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </section>
    </div>
</div>
