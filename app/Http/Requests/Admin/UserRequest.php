<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_users') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:190',
                Rule::unique(User::class, 'email')->ignore($user?->id),
            ],
            'role' => ['required', Rule::enum(Role::class)],
            'password' => [
                $user ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('admin.users.field_name'),
            'email' => __('admin.users.field_email'),
            'role' => __('admin.users.field_role'),
            'password' => __('admin.users.field_password'),
        ];
    }
}
