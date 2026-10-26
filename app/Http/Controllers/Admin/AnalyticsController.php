<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analytics): View
    {
        $summary = $analytics->whatsappClicksSummary(7);

        $topProducts = AnalyticsEvent::query()
            ->where('event_name', 'whatsapp_order_click')
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->whereNotNull('product_id')
            ->select('product_id', DB::raw('COUNT(*) as clicks'), DB::raw('MAX(created_at) as last_click'))
            ->groupBy('product_id')
            ->orderByDesc('clicks')
            ->with(['product:id,name,slug,category_id', 'product.category:id,name'])
            ->limit(10)
            ->get();

        $recentClicks = AnalyticsEvent::query()
            ->with(['product:id,name,slug', 'variant:id,size,color,product_id'])
            ->latest('created_at')
            ->limit(12)
            ->get();

        return view('admin.analytics.index', [
            'summary' => $summary,
            'topProducts' => $topProducts,
            'recentClicks' => $recentClicks,
            'maxDaily' => max(1, collect($summary['last_7_days'])->max('count') ?? 1),
        ]);
    }
}
