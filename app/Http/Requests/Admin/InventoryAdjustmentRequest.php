<?php

namespace App\Http\Requests\Admin;

use App\Services\InventoryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_inventory') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'variant_id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')],
            'mode' => ['required', Rule::in(InventoryService::MODES)],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'reason' => ['required', Rule::in(InventoryService::REASONS)],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'variant_id' => __('admin.inventory.select_variant'),
            'mode' => __('admin.inventory.mode'),
            'quantity' => __('admin.inventory.quantity'),
            'reason' => __('admin.inventory.reason'),
            'note' => __('admin.inventory.note'),
        ];
    }
}
