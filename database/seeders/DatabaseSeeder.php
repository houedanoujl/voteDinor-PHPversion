<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer ou mettre à jour l'utilisateur admin (idempotent)
        $adminEmail = env('ADMIN_EMAIL', 'admin@dinor.local');
        $adminName = env('ADMIN_NAME', 'DINOR Admin');
        $adminPassword = env('ADMIN_PASSWORD');

        // Si aucun mot de passe n'est fourni, générer un mot de passe fort et logger
        if (!$adminPassword) {
            $adminPassword = Str::password(32, symbols: true);
            \Log::warning('ADMIN_PASSWORD non défini. Un mot de passe fort a été généré lors du seed.', [
                'email' => $adminEmail,
            ]);
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
            ]
        );

        // Créer des candidats d'exemple
        $this->call([
            CandidateSeeder::class,
        ]);
    }
}
