<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="space-y-6">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="category-details-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                    <x-store.icon name="tags" class="size-4" />
                </span>
                <h2 id="category-details-title" class="text-base font-black">{{ __('admin.categories.title') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.field name="name" :label="__('admin.categories.field_name')" :value="$category?->name" maxlength="120" required />
                <x-admin.select
                    name="parent_id"
                    :label="__('admin.categories.field_parent')"
                    :options="$parents"
                    :selected="$category?->parent_id"
                    :placeholder="__('admin.categories.root_option')"
                />
                <x-admin.field name="slug" :label="__('admin.categories.field_slug')" :value="$category?->slug" :help="__('admin.products.field_slug_hint')" dir="ltr" />
                <x-admin.field
                    name="display_order"
                    type="number"
                    :label="__('admin.categories.field_display_order')"
                    :value="$category?->display_order ?? 0"
                    min="0"
                    step="1"
                    inputmode="numeric"
                    required
                />
                <div class="sm:col-span-2">
                    <x-admin.field
                        name="description"
                        type="textarea"
                        :label="__('admin.categories.field_description')"
                        :value="$category?->description"
                        rows="4"
                        maxlength="2000"
                    />
                </div>
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="category-seo-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="search" class="size-4" />
                </span>
                <h2 id="category-seo-title" class="text-base font-black">{{ __('admin.common.seo') }}</h2>
            </div>
            <div class="grid gap-4">
                <x-admin.field name="seo_title" :label="__('admin.common.title')" :value="$category?->seo_title" maxlength="255" />
                <x-admin.field name="seo_description" type="textarea" :label="__('admin.common.description')" :value="$category?->seo_description" rows="3" maxlength="500" />
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="surface-card p-5" aria-labelledby="category-status-title">
            <h2 id="category-status-title" class="text-base font-black">{{ __('admin.common.status') }}</h2>
            <div class="mt-4 space-y-3">
                <x-admin.toggle name="is_active" :label="__('admin.categories.is_active')" :checked="$category?->is_active ?? true" />
                <x-admin.toggle name="is_featured" :label="__('admin.categories.is_featured')" :checked="$category?->is_featured ?? false" />
            </div>
            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="category-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>

        <section class="surface-card p-5" aria-labelledby="category-image-title">
            <h2 id="category-image-title" class="text-base font-black">{{ __('admin.categories.field_image') }}</h2>
            <p class="mt-1 text-xs leading-5 text-muted">{{ __('admin.categories.field_image_hint') }}</p>

            @if ($category?->image_path)
                <div class="relative mt-4 aspect-[4/3] overflow-hidden rounded-xl border border-line bg-smoke">
                    <img src="{{ media_url($category->image_path) }}" alt="" class="size-full object-cover">
                </div>
                <x-admin.toggle
                    name="remove_image"
                    :label="__('admin.categories.remove_image')"
                    class="mt-3"
                />
            @endif

            <label for="category-image" class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-line bg-smoke/40 px-4 py-6 text-center transition hover:border-ink/30 hover:bg-smoke">
                <x-store.icon name="upload" class="size-5 text-muted" />
                <span class="mt-2 text-xs font-bold">{{ $category?->image_path ? __('admin.categories.update_image') : __('admin.categories.field_image') }}</span>
                <input id="category-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only">
            </label>
            @error('image')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </section>
    </div>
</div>
