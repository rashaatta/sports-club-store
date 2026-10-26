<x-layouts.admin :title="__('admin.analytics.title')">
    <x-admin.page-header :title="__('admin.analytics.title')" :subtitle="__('admin.analytics.subtitle')" />

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-volt/40 bg-volt-soft/50 px-5 py-4 text-sm leading-6 text-ink">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-volt text-ink">
            <x-store.icon name="info" class="size-4" />
        </span>
        <p>{{ __('admin.analytics.note') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card
            :label="__('admin.analytics.total_30d')"
            :value="$summary['total']"
            :hint="__('admin.analytics.clicks_note_short')"
            icon="whatsapp"
            tone="volt"
        />
        <x-admin.stat-card
            :label="__('admin.analytics.today')"
            :value="$summary['today']"
            :hint="__('admin.analytics.last_7_days')"
            icon="chart"
            tone="ink"
        />
        <x-admin.stat-card
            :label="__('admin.analytics.last_7_days')"
            :value="collect($summary['last_7_days'])->sum('count')"
            :hint="__('admin.analytics.clicks_note_short')"
            icon="calendar"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        <div class="space-y-6">
            <section class="surface-card p-5 sm:p-6" aria-labelledby="analytics-chart-title">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 id="analytics-chart-title" class="text-base font-black">{{ __('admin.dashboard.clicks_chart') }}</h2>
                        <p class="mt-1 text-xs leading-5 text-muted">{{ __('admin.analytics.note') }}</p>
                    </div>
                    <span class="badge-muted">{{ collect($summary['last_7_days'])->sum('count') }} {{ __('admin.analytics.count') }}</span>
                </div>

                <div class="mt-6 flex h-48 items-end justify-between gap-2 sm:gap-4">
                    @foreach ($summary['last_7_days'] as $day)
                        <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-[11px] font-bold text-muted">{{ $day['count'] }}</span>
                            <div class="flex w-full items-end justify-center" style="height: 82%">
                                <div
                                    class="w-full max-w-12 rounded-t-lg {{ $day['count'] > 0 ? 'bg-ink' : 'bg-line' }} transition"
                                    style="height: {{ $day['count'] > 0 ? max(8, round(($day['count'] / $maxDaily) * 100)) : 4 }}%"
                                    title="{{ $day['date'] }}: {{ $day['count'] }}"
                                ></div>
                            </div>
                            <span class="truncate text-[10px] font-semibold text-muted">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="surface-card overflow-hidden" aria-labelledby="analytics-top-title">
                <div class="border-b border-line px-5 py-4 sm:px-6">
                    <h2 id="analytics-top-title" class="text-base font-black">{{ __('admin.analytics.top_products') }}</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.dashboard.top_clicked_note') }}</p>
                </div>

                @if ($topProducts->isEmpty())
                    <x-admin.empty-state :title="__('admin.analytics.no_data')" icon="whatsapp" compact />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-admin min-w-[600px]">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.analytics.product') }}</th>
                                    <th>{{ __('admin.analytics.count') }}</th>
                                    <th>{{ __('admin.analytics.last_click') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topProducts as $entry)
                                    <tr>
                                        <td>
                                            @if ($entry->product)
                                                @can('manage_products')
                                                    <a href="{{ route('admin.products.edit', $entry->product) }}" class="text-sm font-bold hover:underline">{{ $entry->product->name }}</a>
                                                @else
                                                    <span class="text-sm font-bold">{{ $entry->product->name }}</span>
                                                @endcan
                                                <span class="mt-0.5 block text-[11px] text-muted">{{ $entry->product->category?->name }}</span>
                                            @else
                                                <span class="text-sm text-muted">{{ __('admin.common.none') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-sm font-black">{{ $entry->clicks }}</span>
                                        </td>
                                        <td class="whitespace-nowrap text-xs text-muted">
                                            {{ \Illuminate\Support\Carbon::parse($entry->last_click)->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <section class="surface-card overflow-hidden self-start" aria-labelledby="analytics-recent-title">
            <div class="border-b border-line px-5 py-4">
                <h2 id="analytics-recent-title" class="text-base font-black">{{ __('admin.analytics.recent_clicks') }}</h2>
                <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.analytics.recent_clicks_note') }}</p>
            </div>

            @if ($recentClicks->isEmpty())
                <x-admin.empty-state :title="__('admin.analytics.no_recent')" icon="chart" compact />
            @else
                <ul class="divide-y divide-line/70">
                    @foreach ($recentClicks as $event)
                        <li class="flex items-start gap-3 px-5 py-3.5">
                            <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-whatsapp/10 text-whatsapp">
                                <x-store.icon name="whatsapp" class="size-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-bold">{{ $event->product?->name ?? __('admin.common.none') }}</p>
                                <p class="mt-0.5 text-[10px] text-muted">
                                    {{ $event->variant ? $event->variant->optionLabel() : __('admin.inventory.simple_product') }}
                                    · {{ __('admin.analytics.source_'.$event->source) }}
                                </p>
                            </div>
                            <span class="shrink-0 text-[10px] text-muted">{{ $event->created_at?->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.admin>
