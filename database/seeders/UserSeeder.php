<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Provider Users
        $providers = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com'],
            ['name' => 'Emma Wilson', 'email' => 'emma@example.com'],
            ['name' => 'Olivia Brown', 'email' => 'olivia@example.com'],
            ['name' => 'Ava Davis', 'email' => 'ava@example.com'],
            ['name' => 'Isabella Miller', 'email' => 'isabella@example.com'],
            ['name' => 'Sophia Garcia', 'email' => 'sophia@example.com'],
            ['name' => 'Mia Rodriguez', 'email' => 'mia@example.com'],
            ['name' => 'Charlotte Martinez', 'email' => 'charlotte@example.com'],
            ['name' => 'Amelia Anderson', 'email' => 'amelia@example.com'],
            ['name' => 'Harper Taylor', 'email' => 'harper@example.com'],
        ];

        foreach ($providers as $provider) {
            User::create([
                'name' => $provider['name'],
                'email' => $provider['email'],
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create Guest Users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Guest User {$i}",
                'email' => "guest{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'guest',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created 1 admin, 10 providers, and 5 guest users');
    }
}

