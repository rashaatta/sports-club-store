<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
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
        $maxPriceRules = ['nullable', 'numeric', 'min:0', 'max:1000000'];

        if ($this->filled('min_price')) {
            $maxPriceRules[] = 'gte:min_price';
        }

        return [
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:150'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'max_price' => $maxPriceRules,
            'sizes' => ['nullable', 'array', 'max:20'],
            'sizes.*' => ['string', 'max:50'],
            'colors' => ['nullable', 'array', 'max:20'],
            'colors.*' => ['string', 'max:50'],
            'availability' => ['nullable', 'in:in_stock,out_of_stock'],
            'featured' => ['nullable', 'boolean'],
            'on_sale' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => is_string($this->q) ? trim($this->q) : $this->q,
            'category' => is_string($this->category) ? trim($this->category) : $this->category,
            'sizes' => array_values(array_filter((array) $this->input('sizes', []), fn ($value) => filled($value))),
            'colors' => array_values(array_filter((array) $this->input('colors', []), fn ($value) => filled($value))),
        ]);
    }
}
