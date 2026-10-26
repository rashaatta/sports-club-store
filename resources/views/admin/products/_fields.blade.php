@php
    $hasVariants = $product?->usesVariants() ?? false;
    $specs = old('specifications', $product?->specificationRows() ?? []);
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="space-y-6">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="product-details-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                    <x-store.icon name="bag" class="size-4" />
                </span>
                <h2 id="product-details-title" class="text-base font-black">{{ __('admin.products.section_details') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.select
                    name="category_id"
                    :label="__('admin.products.field_category')"
                    :options="$categories"
                    :selected="$product?->category_id"
                    :placeholder="__('admin.common.select_placeholder')"
                    required
                />

                <x-admin.field
                    name="name"
                    :label="__('admin.products.field_name')"
                    :value="$product?->name"
                    maxlength="180"
                    required
                />

                <x-admin.field
                    name="slug"
                    :label="__('admin.products.field_slug')"
                    :value="$product?->slug"
                    :help="__('admin.products.field_slug_hint')"
                    dir="ltr"
                />

                <x-admin.field
                    name="sku"
                    :label="__('admin.products.field_sku')"
                    :value="$product?->sku"
                    maxlength="64"
                    dir="ltr"
                    required
                />

                <x-admin.field
                    name="brand"
                    :label="__('admin.products.field_brand')"
                    :value="$product?->brand"
                    maxlength="80"
                />

                <div class="sm:col-span-2">
                    <x-admin.field
                        name="short_description"
                        type="textarea"
                        :label="__('admin.products.field_short_description')"
                        :value="$product?->short_description"
                        :help="__('admin.products.field_short_description_hint')"
                        rows="3"
                        maxlength="300"
                    />
                </div>

                <div class="sm:col-span-2">
                    <x-admin.field
                        name="description"
                        type="textarea"
                        :label="__('admin.products.field_description')"
                        :value="$product?->description"
                        :help="__('admin.products.field_description_hint')"
                        rows="8"
                    />
                </div>
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="product-pricing-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="chart" class="size-4" />
                </span>
                <h2 id="product-pricing-title" class="text-base font-black">{{ __('admin.products.section_pricing') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.field
                    name="price"
                    type="number"
                    :label="__('admin.products.field_price')"
                    :value="$product?->price"
                    step="0.01"
                    min="0"
                    inputmode="decimal"
                    required
                />

                <x-admin.field
                    name="compare_at_price"
                    type="number"
                    :label="__('admin.products.field_compare_at_price')"
                    :value="$product?->compare_at_price"
                    step="0.01"
                    min="0"
                    inputmode="decimal"
                />

                <div>
                    <x-admin.field
                        name="stock_quantity"
                        type="number"
                        :label="__('admin.products.field_stock_quantity')"
                        :value="$product?->stock_quantity ?? 0"
                        :help="$hasVariants ? __('admin.products.variant_stock_hint') : __('admin.products.field_stock_hint')"
                        min="0"
                        step="1"
                        inputmode="numeric"
                        :disabled="$hasVariants"
                    />
                    @if ($hasVariants)
                        <p class="mt-1.5 text-xs font-semibold text-muted">{{ __('admin.products.stock_managed') }}</p>
                    @endif
                </div>

                <x-admin.field
                    name="low_stock_threshold"
                    type="number"
                    :label="__('admin.products.field_low_stock_threshold')"
                    :value="$product?->low_stock_threshold ?? config('store.low_stock_default')"
                    min="0"
                    step="1"
                    inputmode="numeric"
                    :help="__('admin.inventory.threshold_note')"
                    required
                />
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="product-images-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="image" class="size-4" />
                </span>
                <div>
                    <h2 id="product-images-title" class="text-base font-black">{{ $product ? __('admin.products.images_add') : __('admin.products.section_images') }}</h2>
                    <p class="mt-0.5 text-xs text-muted">{{ __('admin.products.field_images_hint') }}</p>
                </div>
            </div>

            <label for="product-images" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-line bg-smoke/40 px-5 py-8 text-center transition hover:border-ink/30 hover:bg-smoke">
                <span class="flex size-11 items-center justify-center rounded-full bg-white text-ink shadow-panel">
                    <x-store.icon name="upload" class="size-5" />
                </span>
                <span class="mt-3 text-sm font-bold">{{ __('admin.products.field_images') }}</span>
                <span class="mt-1 text-xs text-muted">JPG, PNG, WebP · 4 MB</span>
                <input id="product-images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only">
            </label>
            @error('images')
                <p class="field-error">{{ $message }}</p>
            @enderror
            @error('images.*')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="product-specs-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="sliders" class="size-4" />
                </span>
                <div>
                    <h2 id="product-specs-title" class="text-base font-black">{{ __('admin.products.section_specs') }}</h2>
                    <p class="mt-0.5 text-xs text-muted">{{ __('admin.products.spec_hint') }}</p>
                </div>
            </div>

            <div x-data="{ rows: @js($specs) }" class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <label class="sr-only" :for="`spec-key-${index}`">{{ __('admin.products.spec_key') }}</label>
                        <input :id="`spec-key-${index}`" :name="`specifications[${index}][key]`" x-model="row.key" type="text" maxlength="80" placeholder="{{ __('admin.products.spec_key') }}" class="field flex-1">
                        <label class="sr-only" :for="`spec-value-${index}`">{{ __('admin.products.spec_value') }}</label>
                        <input :id="`spec-value-${index}`" :name="`specifications[${index}][value]`" x-model="row.value" type="text" maxlength="255" placeholder="{{ __('admin.products.spec_value') }}" class="field flex-1">
                        <button type="button" class="icon-btn shrink-0 border border-line text-muted hover:bg-danger-soft hover:text-danger" @click="rows.splice(index, 1)" aria-label="{{ __('admin.common.remove') }}">
                            <x-store.icon name="trash" class="size-4" />
                        </button>
                    </div>
                </template>

                <p x-show="rows.length === 0" class="rounded-lg bg-smoke/60 px-4 py-3 text-xs text-muted">{{ __('admin.products.spec_empty') }}</p>

                <button type="button" class="btn-outline btn-sm" @click="rows.push({ key: '', value: '' })">
                    <x-store.icon name="plus" class="size-4" />
                    {{ __('admin.products.spec_add') }}
                </button>
            </div>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="product-seo-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="search" class="size-4" />
                </span>
                <h2 id="product-seo-title" class="text-base font-black">{{ __('admin.products.section_seo') }}</h2>
            </div>

            <div class="grid gap-4">
                <x-admin.field
                    name="seo_title"
                    :label="__('admin.common.title')"
                    :value="$product?->seo_title"
                    :help="__('admin.products.seo_title_hint')"
                    maxlength="255"
                />
                <x-admin.field
                    name="seo_description"
                    type="textarea"
                    :label="__('admin.common.description')"
                    :value="$product?->seo_description"
                    :help="__('admin.products.seo_description_hint')"
                    rows="3"
                    maxlength="500"
                />
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="surface-card p-5" aria-labelledby="product-status-title">
            <h2 id="product-status-title" class="text-base font-black">{{ __('admin.common.status') }}</h2>
            <div class="mt-4 space-y-3">
                <x-admin.toggle
                    name="is_active"
                    :label="__('admin.products.is_active')"
                    :help="__('admin.products.inactive_hint')"
                    :checked="$product?->is_active ?? true"
                />
                <x-admin.toggle
                    name="is_featured"
                    :label="__('admin.products.is_featured')"
                    :checked="$product?->is_featured ?? false"
                />
                <x-admin.toggle
                    name="is_new_arrival"
                    :label="__('admin.products.is_new_arrival')"
                    :checked="$product?->is_new_arrival ?? false"
                />
            </div>

            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="product-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>

        <section class="surface-card p-5" aria-labelledby="product-model-title">
            <div class="flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-xl bg-smoke text-ink">
                    <x-store.icon name="cube" class="size-4" />
                </span>
                <h2 id="product-model-title" class="text-base font-black">{{ __('admin.products.field_model') }}</h2>
            </div>
            <p class="mt-2 text-xs leading-5 text-muted">{{ __('admin.products.field_model_hint') }}</p>

            @if ($product?->model_3d_path)
                <div class="mt-4 rounded-lg bg-smoke/70 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ __('admin.products.model_current') }}</p>
                    <p class="mt-0.5 break-all text-xs font-semibold" dir="ltr">{{ $product->model_3d_path }}</p>
                </div>
            @else
                <p class="mt-4 rounded-lg bg-smoke/70 px-3 py-2.5 text-xs text-muted">{{ __('admin.products.no_model') }}</p>
            @endif

            <label for="product-model" class="mt-4 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-line px-4 py-3 text-xs font-bold transition hover:border-ink/30 hover:bg-smoke">
                <x-store.icon name="upload" class="size-4" />
                GLB / GLTF
                <input id="product-model" type="file" name="model_3d" accept=".glb,.gltf" class="sr-only">
            </label>
            @error('model_3d')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </section>

        <div class="rounded-2xl bg-ink p-5 text-white">
            <p class="flex items-center gap-2 text-xs font-bold text-volt">
                <x-store.icon name="info" class="size-4" />
                {{ __('admin.products.section_variants') }}
            </p>
            <p class="mt-2 text-xs leading-6 text-white/60">{{ __('admin.products.variant_stock_hint') }}</p>
        </div>
    </div>
</div>
