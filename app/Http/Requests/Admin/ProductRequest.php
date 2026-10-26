<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use App\Support\ThreeDModelValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_products') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $specifications = [];

        foreach ((array) $this->input('specifications', []) as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $value = trim((string) ($row['value'] ?? ''));

            if ($key !== '' && $value !== '') {
                $specifications[] = ['key' => $key, 'value' => $value];
            }
        }

        $this->merge(['specifications' => $specifications]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Product|null $product */
        $product = $this->route('product');

        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                Rule::unique(Product::class, 'slug')->ignore($product?->id),
            ],
            'sku' => [
                'required',
                'string',
                'max:64',
                Rule::unique(Product::class, 'sku')->ignore($product?->id),
            ],
            'brand' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:20000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2', 'gt:price'],
            'stock_quantity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
            'specifications' => ['nullable', 'array', 'max:30'],
            'specifications.*.key' => ['required', 'string', 'max:80'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=400,max_width=6000,max_height=6000'],
            'image_alts' => ['nullable', 'array', 'max:10'],
            'image_alts.*' => ['nullable', 'string', 'max:180'],
            'model_3d' => ['nullable', 'file', 'max:20480', 'extensions:glb,gltf'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_new_arrival' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $file = $this->file('model_3d');

                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    return;
                }

                $extension = strtolower($file->extension() ?: '');

                if (! ThreeDModelValidator::isValid((string) $file->getRealPath(), $extension)) {
                    $validator->errors()->add('model_3d', __('admin.products.model_invalid'));
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => __('admin.products.field_category'),
            'name' => __('admin.products.field_name'),
            'slug' => __('admin.products.field_slug'),
            'sku' => __('admin.products.field_sku'),
            'brand' => __('admin.products.field_brand'),
            'short_description' => __('admin.products.field_short_description'),
            'description' => __('admin.products.field_description'),
            'price' => __('admin.products.field_price'),
            'compare_at_price' => __('admin.products.field_compare_at_price'),
            'stock_quantity' => __('admin.products.field_stock_quantity'),
            'low_stock_threshold' => __('admin.products.field_low_stock_threshold'),
            'specifications.*.key' => __('admin.products.spec_key'),
            'specifications.*.value' => __('admin.products.spec_value'),
            'seo_title' => __('admin.common.title'),
            'seo_description' => __('admin.common.description'),
            'images.*' => __('admin.common.images'),
            'model_3d' => __('admin.products.field_model'),
        ];
    }
}
