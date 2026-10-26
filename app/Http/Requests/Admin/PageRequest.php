<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
        /** @var Page|null $page */
        $page = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                Rule::unique(Page::class, 'slug')->ignore($page?->id),
            ],
            'content' => ['required', 'string', 'max:100000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'show_in_footer' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => __('admin.pages.field_title'),
            'slug' => __('admin.pages.field_slug'),
            'content' => __('admin.pages.field_content'),
            'meta_title' => __('admin.pages.field_meta_title'),
            'meta_description' => __('admin.pages.field_meta_description'),
        ];
    }
}
