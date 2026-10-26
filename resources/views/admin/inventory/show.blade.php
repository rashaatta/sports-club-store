<x-layouts.admin :title="__('admin.inventory.show_title', ['name' => $product->name])">
    <x-admin.page-header :title="__('admin.inventory.show_title', ['name' => $product->name])" :subtitle="$product->sku">
        <x-slot:actions>
            @if ($product->is_active)
                <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener" class="btn-outline btn-sm">
                    <x-store.icon name="external" class="size-4" />
                    {{ __('admin.inventory.view_product') }}
                </a>
            @endif
            <a href="{{ route('admin.inventory.index') }}" class="btn-ghost btn-sm">
                <x-store.icon name="chevron-start" class="size-4" />
                {{ __('admin.inventory.back') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        <div class="space-y-6">
            <section class="surface-card p-5 sm:p-6" aria-labelledby="inventory-overview-title">
                <h2 id="inventory-overview-title" class="sr-only">{{ __('admin.inventory.title') }}</h2>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-smoke">
                        @if ($product->primary_image_path)
                            <img src="{{ media_url($product->primary_image_path) }}" alt="" class="size-full object-cover" width="64" height="64">
                        @else
                            <x-store.icon name="image" class="size-6 text-muted" />
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-black">{{ $product->name }}</p>
                        <p class="mt-0.5 text-xs text-muted">{{ $product->category?->name ?? __('admin.common.none') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-muted">{{ __('admin.products.stock_current') }}</p>
                        <p class="text-2xl font-black {{ $product->total_stock > 0 ? '' : 'text-danger' }}">{{ $product->total_stock }}</p>
                    </div>
                    <div>
                        @if (! $product->is_in_stock)
                            <span class="badge-danger">{{ __('admin.inventory.out') }}</span>
                        @elseif ($product->is_low_stock)
                            <span class="badge-warning">{{ __('admin.inventory.low') }}</span>
                        @else
                            <span class="badge-success">{{ __('admin.inventory.in') }}</span>
                        @endif
                    </div>
                </div>

                @if ($product->usesVariants())
                    <div class="mt-6 overflow-x-auto rounded-xl border border-line">
                        <table class="table-admin min-w-[560px]">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.inventory.variant') }}</th>
                                    <th>{{ __('admin.inventory.sku') }}</th>
                                    <th>{{ __('admin.inventory.current_stock') }}</th>
                                    <th>{{ __('admin.common.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->variants as $variant)
                                    <tr id="variant-{{ $variant->id }}" class="scroll-mt-24 transition hover:bg-smoke/40">
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="size-4 rounded-full border border-line" @if ($variant->color_hex) style="background-color: {{ $variant->color_hex }}" @endif></span>
                                                <span class="text-sm font-bold">{{ $variant->optionLabel() ?: __('admin.common.none') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-xs text-muted" dir="ltr">{{ $variant->sku }}</td>
                                        <td>
                                            <span class="text-sm font-black {{ $variant->stock_quantity > 0 ? '' : 'text-danger' }}">{{ $variant->stock_quantity }}</span>
                                        </td>
                                        <td>
                                            @if ($variant->is_active)
                                                <span class="badge-success">{{ __('admin.common.active') }}</span>
                                            @else
                                                <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-5 rounded-xl bg-smoke/70 px-4 py-3 text-xs text-muted">{{ __('admin.inventory.no_variants') }}</p>
                @endif
            </section>

            <section class="surface-card overflow-hidden" aria-labelledby="inventory-adjust-title">
                <div class="border-b border-line px-5 py-4 sm:px-6">
                    <h2 id="inventory-adjust-title" class="text-base font-black">{{ __('admin.inventory.adjust_form_title') }}</h2>
                    <p class="mt-0.5 text-xs leading-5 text-muted">{{ __('admin.inventory.adjust_hint') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.store', $product) }}" class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                    @csrf

                    @if ($product->usesVariants())
                        <div class="sm:col-span-2">
                            <x-admin.select
                                name="variant_id"
                                :label="__('admin.inventory.select_variant')"
                                :options="$product->variants->mapWithKeys(fn ($variant) => [$variant->id => ($variant->optionLabel() ?: __('admin.common.none')).' — '.$variant->sku])"
                                :placeholder="__('admin.inventory.select_placeholder')"
                                :help="__('admin.inventory.variant_required')"
                                required
                            />
                        </div>
                    @else
                        <div class="sm:col-span-2">
                            <p class="rounded-lg bg-smoke/70 px-4 py-3 text-xs text-muted">{{ __('admin.inventory.no_variants') }}</p>
                        </div>
                    @endif

                    <x-admin.select
                        name="mode"
                        :label="__('admin.inventory.mode')"
                        :options="collect(\App\Services\InventoryService::MODES)->mapWithKeys(fn ($mode) => [$mode => __('admin.inventory.mode_'.$mode)])"
                        :selected="'add'"
                        :help="__('admin.inventory.mode_set_hint')"
                        required
                    />

                    <x-admin.field
                        name="quantity"
                        type="number"
                        :label="__('admin.inventory.quantity')"
                        min="0"
                        step="1"
                        inputmode="numeric"
                        required
                    />

                    <x-admin.select
                        name="reason"
                        :label="__('admin.inventory.reason')"
                        :options="collect($reasons)->mapWithKeys(fn ($reason) => [$reason => __('admin.inventory.reason_'.$reason)])"
                        :selected="'stock_received'"
                        required
                    />

                    <x-admin.field
                        name="note"
                        :label="__('admin.inventory.note')"
                        maxlength="255"
                    />

                    <div class="sm:col-span-2 flex justify-end border-t border-line pt-4">
                        <button type="submit" class="btn-primary">
                            <x-store.icon name="inventory" class="size-4" />
                            {{ __('admin.inventory.save') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <section class="surface-card overflow-hidden self-start" aria-labelledby="inventory-log-title">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <div>
                    <h2 id="inventory-log-title" class="text-base font-black">{{ __('admin.inventory.history') }}</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.inventory.history_note') }}</p>
                </div>
                <x-store.icon name="clock" class="size-5 text-muted" />
            </div>

            @if ($adjustments->isEmpty())
                <x-admin.empty-state :title="__('admin.inventory.history_empty')" icon="inventory" />
            @else
                <div class="overflow-x-auto">
                    <table class="table-admin min-w-[520px]">
                        <thead>
                            <tr>
                                <th>{{ __('admin.inventory.table_date') }}</th>
                                <th>{{ __('admin.inventory.table_change') }}</th>
                                <th>{{ __('admin.inventory.table_after') }}</th>
                                <th>{{ __('admin.inventory.table_reason') }}</th>
                                <th>{{ __('admin.inventory.table_user') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <p class="text-xs font-semibold">{{ $adjustment->created_at?->format('Y-m-d H:i') }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">
                                            {{ $adjustment->variant ? $adjustment->variant->optionLabel() : __('admin.inventory.simple_product') }}
                                        </p>
                                    </td>
                                    <td>
                                        <span @class([
                                            'text-sm font-black',
                                            'text-success' => $adjustment->quantity_change > 0,
                                            'text-danger' => $adjustment->quantity_change < 0,
                                        ])>
                                            {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                                        </span>
                                    </td>
                                    <td class="text-sm font-bold">{{ $adjustment->quantity_after }}</td>
                                    <td>
                                        <span class="badge-muted">{{ __('admin.inventory.reason_'.$adjustment->reason) }}</span>
                                        @if ($adjustment->note)
                                            <p class="mt-1 max-w-[220px] text-[10px] leading-4 text-muted">{{ $adjustment->note }}</p>
                                        @endif
                                    </td>
                                    <td class="text-xs text-muted">{{ $adjustment->user?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-line px-4 py-3">
                    {{ $adjustments->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
