<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_products') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');
        /** @var ProductVariant|null $variant */
        $variant = $this->route('variant');

        return [
            'sku' => [
                'required',
                'string',
                'max:64',
                Rule::unique(ProductVariant::class, 'sku')->ignore($variant?->id),
            ],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'price_override' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'stock_quantity' => [$variant ? 'prohibited' : 'nullable', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'sku' => __('admin.products.variant_sku'),
            'size' => __('admin.products.variant_size'),
            'color' => __('admin.products.variant_color'),
            'color_hex' => __('admin.products.variant_color_hex'),
            'price_override' => __('admin.products.variant_price_override'),
            'stock_quantity' => __('admin.products.variant_initial_stock'),
        ];
    }
}
