<?php

namespace App\Http\Requests\Admin;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_content') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=800,min_height=400,max_width=6000,max_height=6000',
            ],
            'link_url' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (blank($value)) {
                        return;
                    }

                    $safe = str_starts_with($value, '/') && ! str_starts_with($value, '//');
                    $external = preg_match('#^https?://#i', (string) $value) === 1;

                    if (! $safe && ! $external) {
                        $fail(__('admin.banners.invalid_link'));
                    }
                },
            ],
            'button_label' => ['nullable', 'string', 'max:60'],
            'placement' => ['required', 'string', 'in:home_promo'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => __('admin.banners.field_title'),
            'subtitle' => __('admin.banners.field_subtitle'),
            'image' => __('admin.banners.field_image'),
            'link_url' => __('admin.banners.field_link'),
            'button_label' => __('admin.banners.field_button'),
            'placement' => __('admin.banners.field_placement'),
            'starts_at' => __('admin.banners.field_starts_at'),
            'ends_at' => __('admin.banners.field_ends_at'),
        ];
    }
}
