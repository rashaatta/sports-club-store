<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\Product;
use App\Models\ProductVariant;

class AnalyticsService
{
    public function recordWhatsAppOrderClick(Product $product, ?ProductVariant $variant, string $source = 'product_page'): void
    {
        AnalyticsEvent::query()->create([
            'event_name' => 'whatsapp_order_click',
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'source' => $source,
            'created_at' => now(),
        ]);
    }

    /**
     * @return array{total: int, today: int, last_7_days: list<array{date: string, label: string, count: int}>}
     */
    public function whatsappClicksSummary(int $days = 7): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $counts = AnalyticsEvent::query()
            ->where('event_name', 'whatsapp_order_click')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $series[] = [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('D'),
                'count' => (int) ($counts[$date->toDateString()] ?? 0),
            ];
        }

        return [
            'total' => AnalyticsEvent::query()
                ->where('event_name', 'whatsapp_order_click')
                ->where('created_at', '>=', now()->subDays(30)->startOfDay())
                ->count(),
            'today' => (int) ($counts[now()->toDateString()] ?? 0),
            'last_7_days' => $series,
        ];
    }
}
