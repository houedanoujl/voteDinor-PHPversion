<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        // 4 candidats de démonstration (idempotent pour déploiements répétés)
        $candidates = [
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
            Candidate::updateOrCreate(
                ['whatsapp' => $candidateData['whatsapp']],
                $candidateData
            );
        }
    }
}
