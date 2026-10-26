<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class WhatsAppOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $max = (int) config('store.orders.max_quantity', 20);

        return [
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$max],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var Product|null $product */
                $product = $this->route('product');

                if (! $product instanceof Product || ! $product->is_active) {
                    return;
                }

                $quantity = (int) $this->input('quantity', 0);

                if ($product->usesVariants()) {
                    if (! $this->filled('variant_id')) {
                        $validator->errors()->add('variant_id', __('storefront.validation.choose_variant'));

                        return;
                    }

                    $variant = $product->variants()
                        ->whereKey($this->input('variant_id'))
                        ->where('is_active', true)
                        ->first();

                    if ($variant === null) {
                        $validator->errors()->add('variant_id', __('storefront.validation.invalid_variant'));

                        return;
                    }

                    if ($variant->stock_quantity < $quantity) {
                        $validator->errors()->add('quantity', __('storefront.validation.insufficient_stock'));
                    }

                    return;
                }

                if ($this->filled('variant_id')) {
                    $validator->errors()->add('variant_id', __('storefront.validation.invalid_variant'));

                    return;
                }

                if ((int) ($product->stock_quantity ?? 0) < $quantity) {
                    $validator->errors()->add('quantity', __('storefront.validation.insufficient_stock'));
                }
            },
        ];
    }
}
