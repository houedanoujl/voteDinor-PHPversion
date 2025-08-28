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
        // Créer l'utilisateur admin
        User::factory()->create([
            'name' => 'Jean-Luc Admin',
            'email' => 'jeanluc@bigfiveabidjan.com',
            'password' => bcrypt('admin2025!'),
        ]);

        // Créer des candidats d'exemple
        $this->call([
            CandidateSeeder::class,
        ]);
    }
}
