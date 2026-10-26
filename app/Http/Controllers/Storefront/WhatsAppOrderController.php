<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsAppOrderRequest;
use App\Models\Product;
use App\Services\AnalyticsService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class WhatsAppOrderController extends Controller
{
    public function store(
        WhatsAppOrderRequest $request,
        Product $product,
        WhatsAppService $whatsApp,
        AnalyticsService $analytics,
    ): JsonResponse|RedirectResponse {
        abort_unless($product->is_active, 404);

        $quantity = (int) $request->validated('quantity');
        $variant = null;

        if ($product->usesVariants()) {
            $variant = $product->variants()
                ->whereKey($request->validated('variant_id'))
                ->where('is_active', true)
                ->first();

            if ($variant === null || $variant->stock_quantity < $quantity) {
                return $this->unavailableResponse($request);
            }
        } elseif ((int) ($product->stock_quantity ?? 0) < $quantity) {
            return $this->unavailableResponse($request);
        }

        $url = $whatsApp->productOrderUrl($product, $variant, $quantity);

        if ($url === null) {
            return $this->whatsAppUnavailableResponse($request);
        }

        $analytics->recordWhatsAppOrderClick($product, $variant, 'product_page');

        if ($request->expectsJson()) {
            return response()->json(['url' => $url]);
        }

        return redirect()->away($url);
    }

    private function unavailableResponse(WhatsAppOrderRequest $request): JsonResponse|RedirectResponse
    {
        $message = __('storefront.validation.insufficient_stock');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'errors' => ['quantity' => [$message]]], 422);
        }

        return back()->withErrors(['quantity' => $message]);
    }

    private function whatsAppUnavailableResponse(WhatsAppOrderRequest $request): JsonResponse|RedirectResponse
    {
        $message = __('storefront.validation.whatsapp_unavailable');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'errors' => ['whatsapp' => [$message]]], 422);
        }

        return back()->withErrors(['whatsapp' => $message]);
    }
}
