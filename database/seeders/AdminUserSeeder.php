<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('SEED_ADMIN_EMAIL', ''));
        $password = (string) env('SEED_ADMIN_PASSWORD', '');

        if ($email === '' || $password === '') {
            $this->command?->warn('Skipped admin user: set SEED_ADMIN_EMAIL and SEED_ADMIN_PASSWORD to create one.');

            return;
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'مدير المتجر',
                'password' => $password,
                'role' => Role::SuperAdmin,
            ],
        );
    }
}
