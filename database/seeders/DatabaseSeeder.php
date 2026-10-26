<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            StoreSettingsSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
