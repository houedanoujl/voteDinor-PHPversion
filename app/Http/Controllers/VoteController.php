<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class VoteController extends Controller
{
    public function vote(Request $request, $candidateId): JsonResponse
    {
        // Vérifier si les votes sont activés dans les paramètres du site
        $settings = Cache::remember('site_settings', 3600, function () {
            return SiteSetting::first();
        });
        if ($settings && !$settings->votes_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Les votes sont temporairement désactivés'
            ], 403);
        }

        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour voter'
            ], 401);
        }

        try {
            // Récupérer le candidat
            $candidate = Candidate::where('status', 'approved')->findOrFail($candidateId);

            // Vérifier si l'utilisateur a déjà voté aujourd'hui pour ce candidat
            $existingVote = Vote::where('candidate_id', $candidateId)
                ->where('user_id', auth()->id())
                ->whereDate('created_at', today())
                ->first();

            if ($existingVote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà voté pour ce candidat aujourd\'hui'
                ], 400);
            }

            // Créer le vote
            Vote::create([
                'candidate_id' => $candidateId,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);

            // Mettre à jour le compteur de votes du candidat
            $candidate->increment('votes_count');

            return response()->json([
                'success' => true,
                'message' => 'Vote enregistré avec succès !',
                'votes_count' => $candidate->fresh()->votes_count
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du vote: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du vote'
            ], 500);
        }
    }

    public function ranking()
    {
        $candidates = Candidate::approved()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        return response()->json($candidates);
    }
}
