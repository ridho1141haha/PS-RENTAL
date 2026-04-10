<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Normal User
        User::factory()->create([
            'name' => 'Si Paling Rental',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $this->call([
            DeviceSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
