<x-layouts.admin :title="__('admin.dashboard.title')">
    <x-admin.page-header :title="__('admin.dashboard.title')" :subtitle="__('admin.dashboard.subtitle')">
        <x-slot:actions>
            @can('manage_products')
                <a href="{{ route('admin.products.create') }}" class="btn-primary btn-sm">
                    <x-store.icon name="plus" class="size-4" />
                    {{ __('admin.dashboard.add_product') }}
                </a>
            @endcan
            @can('manage_inventory')
                <a href="{{ route('admin.inventory.index') }}" class="btn-outline btn-sm">
                    <x-store.icon name="inventory" class="size-4" />
                    {{ __('admin.dashboard.manage_inventory') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-line/70 bg-white px-5 py-4 shadow-panel">
        <span class="flex size-9 items-center justify-center rounded-full bg-volt text-ink">
            <x-store.icon name="sparkles" class="size-4" />
        </span>
        <div class="min-w-0">
            <p class="text-sm font-black">{{ __('admin.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>
            <p class="mt-0.5 text-xs text-muted">{{ __('admin.brand.panel') }} · {{ auth()->user()->role?->label() }}</p>
        </div>
        <div class="ms-auto flex flex-wrap gap-2">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-ghost btn-sm">
                <x-store.icon name="external" class="size-4" />
                {{ __('admin.nav.view_store') }}
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            :label="__('admin.dashboard.active_products')"
            :value="$activeProductCount"
            :hint="__('admin.dashboard.categories_count').': '.$categoryCount"
            icon="bag"
            tone="volt"
            :href="auth()->user()->can('manage_products') ? route('admin.products.index') : null"
        />
        <x-admin.stat-card
            :label="__('admin.dashboard.out_of_stock')"
            :value="$outOfStockCount"
            :hint="$outOfStockCount > 0 ? __('admin.dashboard.needs_attention') : __('admin.dashboard.healthy')"
            icon="alert"
            :tone="$outOfStockCount > 0 ? 'danger' : 'default'"
            :href="auth()->user()->can('manage_inventory') ? route('admin.inventory.index', ['status' => 'out']) : null"
        />
        <x-admin.stat-card
            :label="__('admin.dashboard.low_stock')"
            :value="$lowStockCount"
            :hint="__('admin.products.low_stock_badge')"
            icon="inventory"
            :href="auth()->user()->can('manage_inventory') ? route('admin.inventory.index', ['status' => 'low']) : null"
        />
        @if ($clicks !== null)
            <x-admin.stat-card
                :label="__('admin.dashboard.clicks_30d')"
                :value="$clicks['total']"
                :hint="__('admin.dashboard.clicks_today').': '.$clicks['today']"
                icon="whatsapp"
                tone="ink"
                :href="route('admin.analytics.index')"
            />
        @elseif ($unreadMessages !== null)
            <x-admin.stat-card
                :label="__('admin.dashboard.unread_messages')"
                :value="$unreadMessages"
                :hint="__('admin.nav.messages')"
                icon="message"
                tone="ink"
                :href="route('admin.messages.index')"
            />
        @else
            <x-admin.stat-card
                :label="__('admin.dashboard.clicks_chart')"
                :value="'—'"
                icon="whatsapp"
                tone="ink"
            />
        @endif
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(0,1fr)]">
        <div class="space-y-6">
            @if ($clicks !== null)
                <section class="surface-card p-5 sm:p-6" aria-labelledby="clicks-title">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 id="clicks-title" class="text-base font-black">{{ __('admin.dashboard.clicks_chart') }}</h2>
                            <p class="mt-1 text-xs leading-5 text-muted">{{ __('admin.dashboard.clicks_note') }}</p>
                        </div>
                        <a href="{{ route('admin.analytics.index') }}" class="text-xs font-bold text-ink underline decoration-ink/20 underline-offset-4 hover:decoration-ink">
                            {{ __('admin.dashboard.view_analytics') }}
                        </a>
                    </div>

                    @php $maxDaily = max(1, collect($clicks['last_7_days'])->max('count') ?? 1); @endphp
                    <div class="mt-6 flex h-40 items-end justify-between gap-2 sm:gap-3">
                        @foreach ($clicks['last_7_days'] as $day)
                            <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                                <span class="text-[10px] font-bold text-muted">{{ $day['count'] }}</span>
                                <div class="flex w-full items-end justify-center" style="height: 80%">
                                    <div
                                        class="w-full max-w-10 rounded-t-lg {{ $day['count'] > 0 ? 'bg-ink' : 'bg-line' }} transition"
                                        style="height: {{ $day['count'] > 0 ? max(8, round(($day['count'] / $maxDaily) * 100)) : 4 }}%"
                                        title="{{ $day['date'] }}: {{ $day['count'] }}"
                                    ></div>
                                </div>
                                <span class="truncate text-[10px] font-semibold text-muted">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="surface-card overflow-hidden" aria-labelledby="top-clicked-title">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                        <div>
                            <h2 id="top-clicked-title" class="text-base font-black">{{ __('admin.dashboard.top_clicked') }}</h2>
                            <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.dashboard.top_clicked_note') }}</p>
                        </div>
                        <x-store.icon name="chart" class="size-5 text-muted" />
                    </div>
                    @if ($topProducts->isEmpty())
                        <x-admin.empty-state :title="__('admin.dashboard.no_clicks')" icon="whatsapp" compact />
                    @else
                        <div class="overflow-x-auto">
                            <table class="table-admin">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin.analytics.product') }}</th>
                                        <th class="text-end!">{{ __('admin.analytics.count') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topProducts as $entry)
                                        <tr>
                                            <td>
                                                @if ($entry->product)
                                                    @can('manage_products')
                                                        <a href="{{ route('admin.products.edit', $entry->product) }}" class="font-bold hover:underline">
                                                            {{ $entry->product->name }}
                                                        </a>
                                                    @else
                                                        <span class="font-bold">{{ $entry->product->name }}</span>
                                                    @endcan
                                                    <span class="mt-0.5 block text-[11px] text-muted">{{ $entry->product->category?->name }}</span>
                                                @else
                                                    <span class="text-muted">{{ __('admin.common.none') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end font-black">{{ $entry->clicks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif

            @can('manage_inventory')
                <section class="surface-card overflow-hidden" aria-labelledby="low-stock-list-title">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                        <div>
                            <h2 id="low-stock-list-title" class="text-base font-black">{{ __('admin.dashboard.low_stock') }}</h2>
                            <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.dashboard.inventory_health') }}</p>
                        </div>
                        <a href="{{ route('admin.inventory.index', ['status' => 'low']) }}" class="text-xs font-bold text-ink underline decoration-ink/20 underline-offset-4 hover:decoration-ink">
                            {{ __('admin.common.view_all') }}
                        </a>
                    </div>
                    @if ($lowStockProducts->isEmpty())
                        <x-admin.empty-state :title="__('admin.dashboard.healthy')" icon="check" compact />
                    @else
                        <ul class="divide-y divide-line/70">
                            @foreach ($lowStockProducts as $product)
                                <li class="flex items-center gap-3 px-5 py-3.5 sm:px-6">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-danger-soft text-danger">
                                        <x-store.icon name="inventory" class="size-4" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold">{{ $product->name }}</p>
                                        <p class="mt-0.5 text-[11px] text-muted">{{ $product->sku }} · {{ $product->category?->name }}</p>
                                    </div>
                                    <span class="badge-danger">{{ $product->total_stock }}</span>
                                    <a href="{{ route('admin.inventory.show', $product) }}" class="btn-ghost btn-sm">{{ __('admin.inventory.manage') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endcan
        </div>

        <div class="space-y-6">
            @if ($unreadMessages !== null)
                <section class="surface-card overflow-hidden" aria-labelledby="recent-messages-title">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                        <div>
                            <h2 id="recent-messages-title" class="text-base font-black">{{ __('admin.dashboard.recent_messages') }}</h2>
                            <p class="mt-0.5 text-[11px] text-muted">{{ $unreadMessages }} {{ __('admin.messages.unread') }}</p>
                        </div>
                        <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold text-ink underline decoration-ink/20 underline-offset-4 hover:decoration-ink">
                            {{ __('admin.common.view_all') }}
                        </a>
                    </div>
                    @if ($recentMessages->isEmpty())
                        <x-admin.empty-state :title="__('admin.dashboard.no_messages')" icon="message" compact />
                    @else
                        <ul class="divide-y divide-line/70">
                            @foreach ($recentMessages as $message)
                                <li>
                                    <a href="{{ route('admin.messages.show', $message) }}" class="flex gap-3 px-5 py-3.5 transition hover:bg-smoke/50">
                                        <span @class([
                                            'mt-1.5 size-2 shrink-0 rounded-full',
                                            'bg-danger' => $message->read_at === null,
                                            'bg-line' => $message->read_at !== null,
                                        ])></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="flex items-center justify-between gap-2">
                                                <span class="truncate text-sm font-bold">{{ $message->name }}</span>
                                                <span class="shrink-0 text-[10px] text-muted">{{ $message->created_at?->diffForHumans() }}</span>
                                            </span>
                                            <span class="mt-0.5 line-clamp-2 text-[11px] leading-5 text-muted">{{ $message->message }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endif

            @if (auth()->user()->can('manage_inventory'))
                <section class="surface-card overflow-hidden" aria-labelledby="recent-adjustments-title">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                        <h2 id="recent-adjustments-title" class="text-base font-black">{{ __('admin.dashboard.recent_adjustments') }}</h2>
                        <a href="{{ route('admin.inventory.index') }}" class="text-xs font-bold text-ink underline decoration-ink/20 underline-offset-4 hover:decoration-ink">
                            {{ __('admin.common.view_all') }}
                        </a>
                    </div>
                    @if ($recentAdjustments->isEmpty())
                        <x-admin.empty-state :title="__('admin.dashboard.no_adjustments')" icon="inventory" compact />
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
                                        <a href="{{ route('admin.inventory.show', $adjustment->product) }}" class="flex size-8 items-center justify-center rounded-lg text-muted transition hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.view') }}">
                                            <x-store.icon name="eye" class="size-4" />
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endif

            <section class="rounded-2xl bg-ink p-5 text-white shadow-panel" aria-labelledby="quick-actions-title">
                <div class="flex items-center gap-3">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                        <x-store.icon name="sliders" class="size-4" />
                    </span>
                    <h2 id="quick-actions-title" class="text-base font-black">{{ __('admin.dashboard.quick_actions') }}</h2>
                </div>
                <div class="mt-4 grid gap-2">
                    @can('manage_products')
                        <a href="{{ route('admin.products.create') }}" class="flex items-center justify-between gap-3 rounded-xl bg-white/5 px-4 py-3 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                            {{ __('admin.dashboard.add_product') }}
                            <x-store.icon name="plus" class="size-4" />
                        </a>
                    @endcan
                    @can('manage_content')
                        <a href="{{ route('admin.settings.edit') }}#homepage" class="flex items-center justify-between gap-3 rounded-xl bg-white/5 px-4 py-3 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                            {{ __('admin.dashboard.edit_homepage') }}
                            <x-store.icon name="home" class="size-4" />
                        </a>
                    @endcan
                    @can('view_messages')
                        <a href="{{ route('admin.messages.index') }}" class="flex items-center justify-between gap-3 rounded-xl bg-white/5 px-4 py-3 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                            {{ __('admin.dashboard.review_messages') }}
                            <x-store.icon name="message" class="size-4" />
                        </a>
                    @endcan
                    @can('manage_settings')
                        <a href="{{ route('admin.settings.edit') }}" class="flex items-center justify-between gap-3 rounded-xl bg-white/5 px-4 py-3 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                            {{ __('admin.nav.settings') }}
                            <x-store.icon name="settings" class="size-4" />
                        </a>
                    @endcan
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>
