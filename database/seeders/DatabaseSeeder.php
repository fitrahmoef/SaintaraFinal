<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed master data first
        $this->call([
            PackageSeeder::class,
            PaymentGatewaySeeder::class,
            CharacterTypeSeeder::class,
            TestSeeder::class,
        ]);

        // Create default test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'user_type' => 'personal',
            ]
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@saintara.com'],
            [
                'name' => 'Admin Saintara',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
                'user_type' => 'admin',
            ]
        );

        // Create instansi user
        User::firstOrCreate(
            ['email' => 'instansi@example.com'],
            [
                'name' => 'Instansi Test',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'user_type' => 'instansi',
            ]
        );
    }
}
