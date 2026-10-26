<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductImagesRequest extends FormRequest
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
        return [
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=400,max_width=6000,max_height=6000'],
            'image_alts' => ['nullable', 'array', 'max:10'],
            'image_alts.*' => ['nullable', 'string', 'max:180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'images' => __('admin.common.images'),
            'images.*' => __('admin.common.images'),
            'image_alts.*' => __('admin.products.field_image_alt'),
        ];
    }
}
