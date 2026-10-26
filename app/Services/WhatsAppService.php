<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Support\Money;

class WhatsAppService
{
    public function normalize(string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if ($digits === '' || str_starts_with($digits, '0')) {
            return null;
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }

    public function number(): ?string
    {
        $raw = StoreSetting::get('whatsapp_number');

        return $raw !== '' ? $this->normalize($raw) : null;
    }

    public function isConfigured(): bool
    {
        return $this->number() !== null;
    }

    public function contactUrl(?string $message = null): ?string
    {
        $number = $this->number();

        if ($number === null) {
            return null;
        }

        $url = 'https://wa.me/'.$number;

        if (filled($message)) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    public function contactMessage(): string
    {
        return __('storefront.whatsapp.contact_message', ['store' => StoreSetting::get('store_name')]);
    }

    public function productOrderUrl(Product $product, ?ProductVariant $variant, int $quantity): ?string
    {
        $number = $this->number();

        if ($number === null) {
            return null;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode(
            $this->productOrderMessage($product, $variant, $quantity),
        );
    }

    public function productOrderMessage(Product $product, ?ProductVariant $variant, int $quantity): string
    {
        $price = $variant?->price_override !== null ? (float) $variant->price_override : (float) $product->price;

        $lines = [
            __('storefront.whatsapp.order_greeting'),
            '',
            __('storefront.whatsapp.product_name').': '.$product->name,
            __('storefront.whatsapp.product_url').': '.route('products.show', $product),
            __('storefront.whatsapp.product_sku').': '.($variant?->sku ?? $product->sku),
            __('storefront.whatsapp.product_price').': '.Money::format($price),
        ];

        if (filled($variant?->size)) {
            $lines[] = __('storefront.whatsapp.size').': '.$variant->size;
        }

        if (filled($variant?->color)) {
            $lines[] = __('storefront.whatsapp.color').': '.$variant->color;
        }

        $lines[] = __('storefront.whatsapp.quantity').': '.$quantity;
        $lines[] = '';
        $lines[] = __('storefront.whatsapp.confirmation');

        return implode("\n", $lines);
    }
}
