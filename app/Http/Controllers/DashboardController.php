<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Photos soumises par l'utilisateur
        $userCandidates = Candidate::where('user_id', $user->id)
            ->withCount('votes')
            ->get();

        // Votes reçus par l'utilisateur
        $totalVotesReceived = $userCandidates->sum('votes_count');

        // Position dans le classement pour chaque candidat
        $candidatesWithRanking = $userCandidates->map(function ($candidate) {
            // Calculer la position dans le classement général
            $betterCandidates = Candidate::approved()
                ->where('votes_count', '>', $candidate->votes_count)
                ->count();

            $candidate->ranking_position = $betterCandidates + 1;
            return $candidate;
        });

        // Meilleure position de l'utilisateur dans le classement
        $bestRankingPosition = null;
        if ($candidatesWithRanking->isNotEmpty()) {
            $bestRankingPosition = $candidatesWithRanking->min('ranking_position');
        }

        // Statistiques personnelles
        $personalStats = [
            'photos_submitted' => $userCandidates->count(),
            'photos_approved' => $userCandidates->where('status', 'approved')->count(),
            'photos_pending' => $userCandidates->where('status', 'pending')->count(),
            'total_votes_received' => $totalVotesReceived,
            'votes_given_today' => Vote::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'ranking_position' => $bestRankingPosition,
        ];

        // Statistiques générales du concours
        $contestStats = [
            'total_candidates' => Candidate::count(),
            'approved_candidates' => Candidate::approved()->count(),
            'total_votes' => Vote::count(),
            'votes_today' => Vote::whereDate('created_at', today())->count(),
        ];

        // Votes reçus par jour (7 derniers jours)
        $votesReceivedChart = Vote::whereIn('candidate_id', $userCandidates->pluck('id'))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // Classement général des candidats (top 10)
        $topCandidates = Candidate::approved()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($candidate, $index) {
                $candidate->position = $index + 1;
                return $candidate;
            });

        return view('dashboard', compact(
            'personalStats',
            'candidatesWithRanking',
            'votesReceivedChart',
            'topCandidates',
            'contestStats'
        ));
    }
}
