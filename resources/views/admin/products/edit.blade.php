<x-layouts.admin :title="__('admin.products.edit_title')">
    <x-admin.page-header :title="__('admin.products.edit_title')" :subtitle="$product->name">
        <x-slot:actions>
            @if ($product->is_active)
                <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener" class="btn-outline btn-sm">
                    <x-store.icon name="external" class="size-4" />
                    {{ __('admin.common.view_store') }}
                </a>
            @endif
            <a href="{{ route('admin.products.index') }}" class="btn-ghost btn-sm">{{ __('admin.products.back_to_products') }}</a>
            <button type="submit" form="product-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="product-form" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._fields')
    </form>

    <div class="mt-6 space-y-6">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="existing-images-title">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="existing-images-title" class="text-base font-black">{{ __('admin.products.images_existing') }}</h2>
                    <p class="mt-0.5 text-xs text-muted">{{ $product->images->count() }} {{ __('admin.common.images') }}</p>
                </div>
                <span class="badge-muted">{{ $product->images->count() }}</span>
            </div>

            @if ($product->images->isEmpty())
                <x-admin.empty-state :title="__('admin.products.no_images')" icon="image" compact />
            @else
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($product->images as $image)
                        <div class="group overflow-hidden rounded-xl border border-line bg-smoke">
                            <div class="relative aspect-square">
                                <img src="{{ media_url($image->path) }}" alt="" class="size-full object-cover" loading="lazy">
                                @if ($loop->first)
                                    <span class="absolute start-2 top-2 badge-volt">{{ __('admin.products.primary_badge') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-2 bg-white px-3 py-2">
                                <span class="truncate text-[10px] text-muted" dir="ltr">{{ basename($image->path) }}</span>
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('{{ __('admin.products.delete_image_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn size-8 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                        <x-store.icon name="trash" class="size-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="variants-title">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="variants-title" class="text-base font-black">{{ __('admin.products.section_variants') }}</h2>
                    <p class="mt-0.5 max-w-2xl text-xs leading-5 text-muted">{{ __('admin.products.variants_section_hint') }}</p>
                </div>
                <span class="badge-ink">{{ __('admin.products.variant_count', ['count' => $product->variants->count()]) }}</span>
            </div>

            @if ($product->variants->isEmpty())
                <x-admin.empty-state :title="__('admin.products.variant_empty')" icon="tags" compact />
            @else
                <div class="mt-5 space-y-3">
                    @foreach ($product->variants as $variant)
                        <div class="rounded-xl border border-line">
                            <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-line" @if ($variant->color_hex) style="background-color: {{ $variant->color_hex }}" @endif>
                                    @unless ($variant->color_hex)
                                        <x-store.icon name="tags" class="size-4 text-muted" />
                                    @endunless
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold">{{ $variant->optionLabel() ?: __('admin.common.none') }}</p>
                                    <p class="mt-0.5 text-[11px] text-muted" dir="ltr">{{ $variant->sku }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[10px] font-bold text-muted">{{ __('admin.products.stock_current') }}</p>
                                    <p class="text-sm font-black {{ $variant->stock_quantity > 0 ? '' : 'text-danger' }}">{{ $variant->stock_quantity }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[10px] font-bold text-muted">{{ __('admin.products.price_display') }}</p>
                                    <p class="text-sm font-black">{{ $variant->price_override ? \App\Support\Money::format($variant->price_override) : '—' }}</p>
                                </div>
                                @if ($variant->is_active)
                                    <span class="badge-success">{{ __('admin.common.active') }}</span>
                                @else
                                    <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                @endif
                                <a href="{{ route('admin.inventory.show', $product) }}#variant-{{ $variant->id }}" class="btn-ghost btn-sm">{{ __('admin.products.actions_inventory') }}</a>
                            </div>

                            <details class="border-t border-line/70">
                                <summary class="cursor-pointer px-4 py-2.5 text-xs font-bold text-muted transition hover:bg-smoke/50 hover:text-ink">
                                    {{ __('admin.products.variant_edit_open') }}
                                </summary>
                                <div class="border-t border-line/70 bg-smoke/30 p-4">
                                    <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label for="variant-{{ $variant->id }}-sku" class="admin-label">{{ __('admin.products.variant_sku') }}</label>
                                            <input id="variant-{{ $variant->id }}-sku" type="text" name="sku" value="{{ $variant->sku }}" maxlength="64" dir="ltr" class="field" required>
                                        </div>
                                        <div>
                                            <label for="variant-{{ $variant->id }}-size" class="admin-label">{{ __('admin.products.variant_size') }}</label>
                                            <input id="variant-{{ $variant->id }}-size" type="text" name="size" value="{{ $variant->size }}" maxlength="50" class="field">
                                        </div>
                                        <div>
                                            <label for="variant-{{ $variant->id }}-color" class="admin-label">{{ __('admin.products.variant_color') }}</label>
                                            <input id="variant-{{ $variant->id }}-color" type="text" name="color" value="{{ $variant->color }}" maxlength="50" class="field">
                                        </div>
                                        <div>
                                            <label for="variant-{{ $variant->id }}-hex" class="admin-label">{{ __('admin.products.variant_color_hex') }}</label>
                                            <input id="variant-{{ $variant->id }}-hex" type="text" name="color_hex" value="{{ $variant->color_hex }}" placeholder="#000000" maxlength="7" dir="ltr" class="field">
                                        </div>
                                        <div>
                                            <label for="variant-{{ $variant->id }}-price" class="admin-label">{{ __('admin.products.variant_price_override') }}</label>
                                            <input id="variant-{{ $variant->id }}-price" type="number" name="price_override" value="{{ $variant->price_override }}" min="0" step="0.01" class="field">
                                        </div>
                                        <div class="flex items-end">
                                            <x-admin.toggle
                                                name="is_active"
                                                :label="__('admin.products.variant_active')"
                                                :checked="$variant->is_active"
                                                :id="'variant-'.$variant->id.'-active'"
                                                class="w-full"
                                            />
                                        </div>
                                        <div class="flex flex-wrap items-end justify-end gap-2 sm:col-span-2 lg:col-span-4">
                                            <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.save') }}</button>
                                        </div>
                                    </form>

                                    <div class="mt-3 border-t border-line/70 pt-3">
                                        @if ($variant->inventory_adjustments_count > 0)
                                            <p class="text-xs text-muted">{{ __('admin.products.variant_delete_blocked') }}</p>
                                        @else
                                            <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirm('{{ __('admin.products.variant_delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger btn-sm">
                                                    <x-store.icon name="trash" class="size-4" />
                                                    {{ __('admin.products.variant_delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endforeach
                </div>
            @endif

            <details class="mt-4 rounded-xl border border-dashed border-ink/20 bg-smoke/30">
                <summary class="flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-bold">
                    <x-store.icon name="plus" class="size-4" />
                    {{ __('admin.products.variant_add') }}
                </summary>
                <div class="border-t border-line p-4">
                    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @csrf
                        <div>
                            <label for="new-variant-sku" class="admin-label">{{ __('admin.products.variant_sku') }}</label>
                            <input id="new-variant-sku" type="text" name="sku" value="{{ old('sku') }}" maxlength="64" dir="ltr" class="field" required>
                        </div>
                        <div>
                            <label for="new-variant-size" class="admin-label">{{ __('admin.products.variant_size') }}</label>
                            <input id="new-variant-size" type="text" name="size" value="{{ old('size') }}" maxlength="50" class="field">
                        </div>
                        <div>
                            <label for="new-variant-color" class="admin-label">{{ __('admin.products.variant_color') }}</label>
                            <input id="new-variant-color" type="text" name="color" value="{{ old('color') }}" maxlength="50" class="field">
                        </div>
                        <div>
                            <label for="new-variant-hex" class="admin-label">{{ __('admin.products.variant_color_hex') }}</label>
                            <input id="new-variant-hex" type="text" name="color_hex" value="{{ old('color_hex') }}" placeholder="#000000" maxlength="7" dir="ltr" class="field">
                        </div>
                        <div>
                            <label for="new-variant-price" class="admin-label">{{ __('admin.products.variant_price_override') }}</label>
                            <input id="new-variant-price" type="number" name="price_override" value="{{ old('price_override') }}" min="0" step="0.01" class="field">
                        </div>
                        <div>
                            <label for="new-variant-stock" class="admin-label">{{ __('admin.products.variant_initial_stock') }}</label>
                            <input id="new-variant-stock" type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" step="1" class="field">
                        </div>
                        <div class="flex items-end">
                            <x-admin.toggle
                                name="is_active"
                                :label="__('admin.products.variant_active')"
                                :checked="true"
                                id="new-variant-active"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-end justify-end">
                            <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.create') }}</button>
                        </div>
                    </form>
                </div>
            </details>
        </section>

        <section class="rounded-2xl border border-danger/20 bg-danger-soft/60 p-5 sm:p-6" aria-labelledby="product-danger-title">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 id="product-danger-title" class="text-base font-black text-danger">{{ __('admin.products.danger_title') }}</h2>
                    <p class="mt-1 max-w-2xl text-xs leading-5 text-danger/80">{{ __('admin.products.danger_hint') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('{{ __('admin.products.delete_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <x-store.icon name="trash" class="size-4" />
                        {{ __('admin.products.delete_product') }}
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.admin>
