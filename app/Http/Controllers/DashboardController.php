<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Photos soumises par l'utilisateur avec eager loading optimisé
        $userCandidates = Candidate::where('user_id', $user->id)
            ->with(['votes' => function ($query) {
                $query->select('candidate_id', 'created_at');
            }])
            ->get();

        // Votes reçus par l'utilisateur (calculé directement)
        $totalVotesReceived = $userCandidates->sum('votes_count');

        // Optimisation : Calculer toutes les positions avec une seule requête
        $candidatesWithRanking = $this->calculateRankingsOptimized($userCandidates);

        // Meilleure position de l'utilisateur dans le classement
        $bestRankingPosition = null;
        if ($candidatesWithRanking->isNotEmpty()) {
            $bestRankingPosition = $candidatesWithRanking->min('ranking_position');
        }

        // Statistiques personnelles (optimisées)
        $votesGivenToday = Cache::remember(
            "user_{$user->id}_votes_today_" . now()->toDateString(),
            300, // 5 minutes
            fn() => Vote::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count()
        );

        $personalStats = [
            'photos_submitted' => $userCandidates->count(),
            'photos_approved' => $userCandidates->where('status', 'approved')->count(),
            'photos_pending' => $userCandidates->where('status', 'pending')->count(),
            'total_votes_received' => $totalVotesReceived,
            'votes_given_today' => $votesGivenToday,
            'ranking_position' => $bestRankingPosition,
        ];

        // Statistiques générales du concours (mises en cache)
        $contestStats = Cache::remember('contest_stats', 600, function () { // 10 minutes
            return [
                'total_candidates' => Candidate::count(),
                'approved_candidates' => Candidate::approved()->count(),
                'total_votes' => Vote::count(),
                'votes_today' => Vote::whereDate('created_at', today())->count(),
            ];
        });

        // Votes reçus par jour (7 derniers jours) - optimisé
        $votesReceivedChart = $this->getVotesChartOptimized($userCandidates);

        // Classement général des candidats (top 10) - mis en cache
        $topCandidates = Cache::remember('top_candidates_10', 300, function () { // 5 minutes
            return Candidate::approved()
                ->orderBy('votes_count', 'desc')
                ->take(10)
                ->get(['id', 'nom', 'prenom', 'votes_count', 'photo_url', 'photo_filename'])
                ->map(function ($candidate, $index) {
                    $candidate->position = $index + 1;
                    return $candidate;
                });
        });

        return view('dashboard', compact(
            'personalStats',
            'candidatesWithRanking',
            'votesReceivedChart',
            'topCandidates',
            'contestStats'
        ));
    }

    /**
     * Calcule les positions de classement de manière optimisée
     */
    private function calculateRankingsOptimized($userCandidates)
    {
        if ($userCandidates->isEmpty()) {
            return collect();
        }

        // Récupérer tous les votes counts des candidats approuvés une seule fois
        $allVotesCounts = Cache::remember('all_approved_votes_counts', 300, function () {
            return Candidate::approved()
                ->pluck('votes_count')
                ->sort()
                ->values()
                ->reverse()
                ->toArray();
        });

        // Calculer la position pour chaque candidat de l'utilisateur
        return $userCandidates->map(function ($candidate) use ($allVotesCounts) {
            // Compter combien de candidats ont plus de votes
            $betterCount = 0;
            foreach ($allVotesCounts as $voteCount) {
                if ($voteCount > $candidate->votes_count) {
                    $betterCount++;
                } else {
                    break;
                }
            }
            
            $candidate->ranking_position = $betterCount + 1;
            return $candidate;
        });
    }

    /**
     * Optimise la récupération des données du graphique de votes
     */
    private function getVotesChartOptimized($userCandidates)
    {
        if ($userCandidates->isEmpty()) {
            return collect();
        }

        $candidateIds = $userCandidates->pluck('id')->toArray();
        
        return Cache::remember(
            'votes_chart_' . md5(implode(',', $candidateIds)) . '_' . now()->toDateString(),
            1800, // 30 minutes
            function () use ($candidateIds) {
                return Vote::whereIn('candidate_id', $candidateIds)
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->whereBetween('created_at', [now()->subDays(7), now()])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->pluck('count', 'date');
            }
        );
    }
}
