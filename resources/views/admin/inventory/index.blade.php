<x-layouts.admin :title="__('admin.inventory.title')">
    <x-admin.page-header :title="__('admin.inventory.title')" :subtitle="__('admin.inventory.subtitle')" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            :label="__('admin.inventory.summary_total')"
            :value="$counts['total']"
            :hint="__('admin.inventory.summary_hint')"
            icon="inventory"
            tone="volt"
        />
        <x-admin.stat-card
            :label="__('admin.inventory.summary_low')"
            :value="$counts['low']"
            :hint="__('admin.inventory.low')"
            icon="alert"
            :tone="$counts['low'] > 0 ? 'danger' : 'default'"
            :href="route('admin.inventory.index', ['status' => 'low'])"
        />
        <x-admin.stat-card
            :label="__('admin.inventory.summary_out')"
            :value="$counts['out']"
            :hint="__('admin.inventory.out')"
            icon="box"
            :tone="$counts['out'] > 0 ? 'danger' : 'default'"
            :href="route('admin.inventory.index', ['status' => 'out'])"
        />
        <x-admin.stat-card
            :label="__('admin.inventory.summary_variants')"
            :value="$counts['variants']"
            :hint="__('admin.inventory.variants_table')"
            icon="tags"
            tone="ink"
        />
    </div>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_200px_auto]">
            <div>
                <label for="inventory-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="inventory-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.inventory.filter_search') }}" class="field ps-10">
                </div>
            </div>
            <div>
                <label for="inventory-status" class="sr-only">{{ __('admin.inventory.filter_status') }}</label>
                <select id="inventory-status" name="status" class="field min-h-11">
                    <option value="all" @selected($status === 'all')>{{ __('admin.inventory.all') }}</option>
                    <option value="in" @selected($status === 'in')>{{ __('admin.inventory.in') }}</option>
                    <option value="low" @selected($status === 'low')>{{ __('admin.inventory.low') }}</option>
                    <option value="out" @selected($status === 'out')>{{ __('admin.inventory.out') }}</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.apply') }}</button>
                @if ($search !== '' || $status !== 'all')
                    <a href="{{ route('admin.inventory.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
        <div class="surface-card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <h2 class="text-base font-black">{{ __('admin.inventory.title') }}</h2>
                <span class="badge-muted">{{ $products->total() }}</span>
            </div>

            @if ($products->isEmpty())
                <x-admin.empty-state :title="__('admin.inventory.empty')" icon="inventory" />
            @else
                <div class="overflow-x-auto">
                    <table class="table-admin min-w-[720px]">
                        <thead>
                            <tr>
                                <th>{{ __('admin.inventory.table_product') }}</th>
                                <th>{{ __('admin.inventory.table_stock') }}</th>
                                <th>{{ __('admin.inventory.table_threshold') }}</th>
                                <th>{{ __('admin.inventory.table_status') }}</th>
                                <th class="text-end!">{{ __('admin.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="transition hover:bg-smoke/40">
                                    <td>
                                        <p class="max-w-[260px] truncate text-sm font-bold">{{ $product->name }}</p>
                                        <p class="mt-0.5 text-[11px] text-muted">
                                            <span dir="ltr">{{ $product->sku }}</span>
                                            @if ($product->category)
                                                · {{ $product->category->name }}
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <span class="text-sm font-black">{{ $product->total_stock }}</span>
                                        @if ($product->usesVariants())
                                            <span class="mt-0.5 block text-[10px] text-muted">{{ __('admin.products.variant_count', ['count' => $product->variants_count]) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-sm text-muted">{{ $product->low_stock_threshold }}</td>
                                    <td>
                                        @if (! $product->is_in_stock)
                                            <span class="badge-danger">{{ __('admin.inventory.out') }}</span>
                                        @elseif ($product->is_low_stock)
                                            <span class="badge-warning">{{ __('admin.inventory.low') }}</span>
                                        @else
                                            <span class="badge-success">{{ __('admin.inventory.in') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.inventory.show', $product) }}" class="btn-outline btn-sm">
                                            {{ __('admin.inventory.manage') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-line px-4 py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <section class="surface-card overflow-hidden self-start" aria-labelledby="inventory-history-title">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <div>
                    <h2 id="inventory-history-title" class="text-base font-black">{{ __('admin.inventory.history_all') }}</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.inventory.history_note') }}</p>
                </div>
                <x-store.icon name="clock" class="size-5 text-muted" />
            </div>

            @if ($recentAdjustments->isEmpty())
                <x-admin.empty-state :title="__('admin.inventory.history_all_empty')" icon="inventory" compact />
            @else
                <ul class="divide-y divide-line/70">
                    @foreach ($recentAdjustments as $adjustment)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <span @class([
                                'flex size-8 shrink-0 items-center justify-center rounded-lg text-xs font-black',
                                'bg-success-soft text-success' => $adjustment->quantity_change > 0,
                                'bg-danger-soft text-danger' => $adjustment->quantity_change < 0,
                            ])>
                                {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-bold">{{ $adjustment->product?->name }}</p>
                                <p class="mt-0.5 text-[10px] text-muted">
                                    {{ $adjustment->variant ? $adjustment->variant->optionLabel() : __('admin.inventory.simple_product') }}
                                    · {{ $adjustment->created_at?->diffForHumans() }}
                                </p>
                            </div>
                            @if ($adjustment->product)
                                <a href="{{ route('admin.inventory.show', $adjustment->product) }}" class="icon-btn size-8 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.view') }}">
                                    <x-store.icon name="eye" class="size-4" />
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.admin>
