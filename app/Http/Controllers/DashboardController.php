<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_candidates' => Candidate::count(),
            'approved_candidates' => Candidate::approved()->count(),
            'pending_candidates' => Candidate::where('status', 'pending')->count(),
            'total_votes' => Vote::count(),
            'total_users' => User::count(),
            'votes_today' => Vote::whereDate('created_at', today())->count(),
        ];

        // Top 5 candidats par votes
        $topCandidates = Candidate::approved()
            ->orderBy('votes_count', 'desc')
            ->take(5)
            ->get();

        // Votes par jour (7 derniers jours)
        $votesChart = Vote::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // Candidatures par jour (7 derniers jours)
        $candidatesChart = Candidate::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        return view('dashboard', compact(
            'stats', 
            'topCandidates', 
            'votesChart', 
            'candidatesChart'
        ));
    }
}