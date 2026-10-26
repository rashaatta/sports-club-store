<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_categories') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Category|null $category */
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                Rule::unique(Category::class, 'slug')->ignore($category?->id),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists(Category::class, 'id')->whereNull('parent_id'),
                Rule::notIn(array_filter([$category?->id])),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=400,max_width=6000,max_height=6000'],
            'remove_image' => ['sometimes', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('admin.categories.field_name'),
            'slug' => __('admin.categories.field_slug'),
            'parent_id' => __('admin.categories.field_parent'),
            'description' => __('admin.categories.field_description'),
            'image' => __('admin.categories.field_image'),
            'display_order' => __('admin.categories.field_display_order'),
            'seo_title' => __('admin.common.title'),
            'seo_description' => __('admin.common.description'),
        ];
    }
}
