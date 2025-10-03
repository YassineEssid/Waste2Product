<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@waste2product.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+216 20 123 456',
                'address' => 'Tunis, Tunisia',
                'bio' => 'Administrateur principal de la plateforme Waste2Product. Gestionnaire de la communauté et superviseur des opérations.',
            ]
        );

        // Create additional test users
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+216 21 234 567',
                'address' => 'Ariana, Tunisia',
                'bio' => 'Utilisateur membre de la communauté Waste2Product.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'repairer@test.com'],
            [
                'name' => 'Réparateur Expert',
                'password' => Hash::make('password'),
                'role' => 'repairer',
                'phone' => '+216 22 345 678',
                'address' => 'Sousse, Tunisia',
                'bio' => 'Expert en réparation d\'électroménager et d\'appareils électroniques avec 10 ans d\'expérience.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'artisan@test.com'],
            [
                'name' => 'Artisan Créateur',
                'password' => Hash::make('password'),
                'role' => 'artisan',
                'phone' => '+216 23 456 789',
                'address' => 'Sfax, Tunisia',
                'bio' => 'Artisan spécialisé dans la transformation de déchets en objets d\'art et produits utiles.',
            ]
        );

        $this->command->info('✅ Utilisateurs admin et de test créés avec succès!');
        $this->command->line('');
        $this->command->line('📧 Identifiants de connexion:');
        $this->command->line('─────────────────────────────');
        $this->command->line('🔐 Admin: admin@waste2product.com / password');
        $this->command->line('👤 User: user@test.com / password');
        $this->command->line('🔧 Repairer: repairer@test.com / password');
        $this->command->line('🎨 Artisan: artisan@test.com / password');
    }
}

