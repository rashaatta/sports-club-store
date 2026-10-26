<?php

namespace App\Support;

use App\Models\StoreSetting;

final class Money
{
    public static function format(float|int|string|null $amount, ?string $symbol = null): string
    {
        $formatted = number_format((float) $amount, 2, '.', ',');

        $symbol = $symbol ?? StoreSetting::get('currency_symbol', 'ر.س');

        if ($symbol === 'ر.س' && app()->getLocale() === 'en') {
            $symbol = 'SAR';
        }

        return $symbol !== '' ? $formatted.' '.$symbol : $formatted;
    }

    public static function discountPercent(float|int|string $price, float|int|string|null $compareAt): ?int
    {
        $price = (float) $price;
        $compareAt = (float) $compareAt;

        if ($compareAt <= 0 || $price >= $compareAt) {
            return null;
        }

        return (int) round((($compareAt - $price) / $compareAt) * 100);
    }
}
