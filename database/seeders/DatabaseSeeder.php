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
        $adminWhatsapp = env('ADMIN_WHATSAPP');

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
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',   // requis pour l'accès Filament
                'type' => 'admin',   // cohérence du type
                'whatsapp' => $adminWhatsapp,
            ]
        );

        // Créer des candidats d'exemple
        $this->call([
            CandidateSeeder::class,
        ]);
    }
}
