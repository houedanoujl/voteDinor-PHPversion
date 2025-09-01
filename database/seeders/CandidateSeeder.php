<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $candidates = [
            [
                'prenom' => 'Adjoua',
                'nom' => 'Kouassi',
                'email' => 'adjoua.kouassi@example.com',
                'whatsapp' => '+22507123456',
                'description' => 'Passionnée de cuisine traditionnelle ivoirienne depuis mon enfance. J\'adore revisiter les plats de grand-mère avec une touche moderne.',
                'photo_url' => '/images/avatar-1.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Koffi',
                'nom' => 'Assouan',
                'email' => 'koffi.assouan@example.com',
                'whatsapp' => '+22507234567',
                'description' => 'Chef cuisinier professionnel, spécialisé dans la fusion entre cuisine française et africaine. 15 ans d\'expérience.',
                'photo_url' => '/images/avatar-2.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Fatou',
                'nom' => 'Traoré',
                'email' => 'fatou.traore@example.com',
                'whatsapp' => '+22507345678',
                'description' => 'Amoureuse des saveurs authentiques de Côte d\'Ivoire. Je cuisine avec le cœur et partage mes recettes familiales.',
                'photo_url' => '/images/avatar-3.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Moussa',
                'nom' => 'Diabaté',
                'email' => 'moussa.diabate@example.com',
                'whatsapp' => '+22507456789',
                'description' => 'Étudiant en hôtellerie-restauration, passionné par l\'art culinaire et les traditions gastronomiques ivoiriennes.',
                'photo_url' => '/images/avatar-4.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Aminata',
                'nom' => 'Koné',
                'email' => 'aminata.kone@example.com',
                'whatsapp' => '+22507567890',
                'description' => 'Blogueuse culinaire et photographe. J\'immortalise les plats traditionnels avec un œil artistique moderne.',
                'photo_url' => '/images/avatar-5.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Ibrahim',
                'nom' => 'Ouattara',
                'email' => 'ibrahim.ouattara@example.com',
                'whatsapp' => '+22507678901',
                'description' => 'Restaurateur depuis 10 ans, je perpétue les traditions culinaires tout en innovant pour les nouvelles générations.',
                'photo_url' => '/images/avatar-6.svg',
                'votes_count' => 0,
                'status' => 'pending',
            ],
            // Ajouts: 4 candidats supplémentaires approuvés pour les tests de vote
            [
                'prenom' => 'Nadia',
                'nom' => 'Bamba',
                'email' => 'nadia.bamba@example.com',
                'whatsapp' => '+22507789012',
                'description' => 'Passionnée de pâtisserie et de douceurs ivoiriennes.',
                'photo_url' => '/images/avatar-1.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Serge',
                'nom' => 'Koulibaly',
                'email' => 'serge.koulibaly@example.com',
                'whatsapp' => '+22507890123',
                'description' => 'Amateur de cuisine traditionnelle, j\'aime revisiter les classiques.',
                'photo_url' => '/images/avatar-2.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Clarisse',
                'nom' => 'Yao',
                'email' => 'clarisse.yao@example.com',
                'whatsapp' => '+22507901234',
                'description' => 'Photographe culinaire, je mets en valeur les plats de chez nous.',
                'photo_url' => '/images/avatar-3.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Yao',
                'nom' => 'Aka',
                'email' => 'yao.aka@example.com',
                'whatsapp' => '+22507012345',
                'description' => 'Étudiant passionné de cuisine, toujours prêt à expérimenter.',
                'photo_url' => '/images/avatar-4.svg',
                'votes_count' => 0,
                'status' => 'approved',
            ],
        ];

        foreach ($candidates as $candidateData) {
            Candidate::create($candidateData);
        }
    }
}
