<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::ProductManager,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => ['role' => Role::SuperAdmin]);
    }

    public function storeManager(): static
    {
        return $this->state(fn () => ['role' => Role::StoreManager]);
    }

    public function inventoryManager(): static
    {
        return $this->state(fn () => ['role' => Role::InventoryManager]);
    }

    public function contentManager(): static
    {
        return $this->state(fn () => ['role' => Role::ContentManager]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
