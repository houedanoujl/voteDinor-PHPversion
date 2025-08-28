<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function vote(Request $request, Candidate $candidate)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Vous devez être connecté pour voter'], 401);
        }

        // Vérifier si l'utilisateur peut voter pour ce candidat aujourd'hui
        if (!$user->canVoteForCandidate($candidate->id)) {
            return response()->json(['error' => 'Vous avez déjà voté pour ce candidat aujourd\'hui'], 409);
        }

        // Créer le vote
        Vote::create([
            'user_id' => $user->id,
            'candidate_id' => $candidate->id,
            'ip_address' => $request->ip(),
        ]);

        // Incrémenter le compteur de votes du candidat
        $candidate->increment('votes_count');

        return response()->json([
            'success' => true,
            'message' => 'Vote enregistré avec succès',
            'votes_count' => $candidate->refresh()->votes_count
        ]);
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