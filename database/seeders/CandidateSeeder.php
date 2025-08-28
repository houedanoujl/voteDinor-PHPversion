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
                'photo_url' => 'https://images.unsplash.com/photo-1494790108755-2616c78d5823?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Koffi',
                'nom' => 'Assouan',
                'email' => 'koffi.assouan@example.com',
                'whatsapp' => '+22507234567',
                'description' => 'Chef cuisinier professionnel, spécialisé dans la fusion entre cuisine française et africaine. 15 ans d\'expérience.',
                'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Fatou',
                'nom' => 'Traoré',
                'email' => 'fatou.traore@example.com',
                'whatsapp' => '+22507345678',
                'description' => 'Amoureuse des saveurs authentiques de Côte d\'Ivoire. Je cuisine avec le cœur et partage mes recettes familiales.',
                'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Moussa',
                'nom' => 'Diabaté',
                'email' => 'moussa.diabate@example.com',
                'whatsapp' => '+22507456789',
                'description' => 'Étudiant en hôtellerie-restauration, passionné par l\'art culinaire et les traditions gastronomiques ivoiriennes.',
                'photo_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Aminata',
                'nom' => 'Koné',
                'email' => 'aminata.kone@example.com',
                'whatsapp' => '+22507567890',
                'description' => 'Blogueuse culinaire et photographe. J\'immortalise les plats traditionnels avec un œil artistique moderne.',
                'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'approved',
            ],
            [
                'prenom' => 'Ibrahim',
                'nom' => 'Ouattara',
                'email' => 'ibrahim.ouattara@example.com',
                'whatsapp' => '+22507678901',
                'description' => 'Restaurateur depuis 10 ans, je perpétue les traditions culinaires tout en innovant pour les nouvelles générations.',
                'photo_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop&crop=face',
                'votes_count' => 0,
                'status' => 'pending',
            ]
        ];

        foreach ($candidates as $candidateData) {
            Candidate::create($candidateData);
        }
    }
}