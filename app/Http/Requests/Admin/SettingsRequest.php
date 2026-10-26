<?php

namespace App\Http\Requests\Admin;

use App\Services\WhatsAppService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->targetGroup() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $group = $this->targetGroup();
        $fields = config('store.settings.'.$group.'.fields', []);
        $rules = [];

        foreach ($fields as $key => $definition) {
            $rules[$key] = match ($definition['type']) {
                'toggle' => ['nullable', 'boolean'],
                'url' => ['nullable', 'url', 'max:500'],
                'textarea' => ['nullable', 'string', 'max:2000'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=400,max_width=6000,max_height=6000'],
                'raw_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        if (array_key_exists('whatsapp_number', $rules)) {
            $rules['whatsapp_number'][] = function (string $attribute, mixed $value, Closure $fail) {
                if (blank($value)) {
                    return;
                }

                if (app(WhatsAppService::class)->normalize((string) $value) === null) {
                    $fail(__('admin.settings.whatsapp_invalid'));
                }
            };
        }

        if (array_key_exists('contact_email', $rules)) {
            $rules['contact_email'][] = 'email';
            $rules['contact_email'][] = 'max:190';
        }

        if (array_key_exists('currency_code', $rules)) {
            $rules['currency_code'][] = 'size:3';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $group = $this->input('group');
        $fields = config('store.settings.'.(is_string($group) ? $group : '').'.fields', []);

        foreach ($fields as $key => $definition) {
            if ($definition['type'] === 'toggle' && ! $this->has($key)) {
                $this->merge([$key => '0']);
            }
        }

        $this->merge(['group' => $group]);
    }

    public function targetGroup(): ?string
    {
        $group = $this->input('group');

        if (! is_string($group) || ! array_key_exists($group, config('store.settings', []))) {
            return null;
        }

        $permission = config('store.settings.'.$group.'.permission');

        if ($permission === null || ! $this->user()?->can($permission)) {
            return null;
        }

        return $group;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys($this->rules()) as $key) {
            $attributes[$key] = __('admin.settings.field_'.$key);
        }

        return $attributes;
    }
}
