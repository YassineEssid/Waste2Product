<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing test users (optional - be careful in production)
        User::where('email', 'like', '%@test.com')->delete();

        // Create test users for different roles
        User::create([
            'name' => 'Community User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'address' => 'New York, USA',
            'email_verified_at' => now(),
        ]);

            // Add requested test user
            User::create([
                'name' => 'Test Example User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'address' => 'Paris, France',
                'email_verified_at' => now(),
            ]);

        User::create([
            'name' => 'Expert Repairer',
            'email' => 'repairer@test.com',
            'password' => Hash::make('password'),
            'role' => 'repairer',
            'address' => 'Los Angeles, USA',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Creative Artisan',
            'email' => 'artisan@test.com',
            'password' => Hash::make('password'),
            'role' => 'artisan',
            'address' => 'San Francisco, USA',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'address' => 'Chicago, USA',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->line('Login credentials:');
        $this->command->line('1. Community User: user@test.com / password');
        $this->command->line('2. Expert Repairer: repairer@test.com / password');
        $this->command->line('3. Creative Artisan: artisan@test.com / password');
        $this->command->line('4. System Admin: admin@test.com / password');
    }
}
