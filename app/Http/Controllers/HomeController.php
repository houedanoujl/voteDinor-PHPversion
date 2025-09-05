<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Mise en cache des paramètres du site
        $settings = Cache::remember('site_settings', 3600, function () { // 1 heure
            return SiteSetting::first();
        });

        // Mise en cache des statistiques de la page d'accueil
        $stats = Cache::remember('home_stats', 300, function () { // 5 minutes
            return [
                'total_candidates' => Candidate::approved()->count(),
                'total_votes' => Vote::count(),
                'top_candidates' => Candidate::approved()
                    ->orderBy('votes_count', 'desc')
                    ->select(['id', 'prenom', 'nom', 'votes_count', 'photo_url', 'photo_filename'])
                    ->take(3)
                    ->get()
            ];
        });

        return view('contest.home', [
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    public function ranking()
    {
        // Mise en cache du classement complet avec pagination éventuelle
        $candidates = Cache::remember('ranking_all_candidates', 300, function () { // 5 minutes
            return Candidate::approved()
                ->orderBy('votes_count', 'desc')
                ->select(['id', 'prenom', 'nom', 'votes_count', 'photo_url', 'photo_filename', 'description'])
                ->get()
                ->map(function ($candidate, $index) {
                    $candidate->position = $index + 1;
                    return $candidate;
                });
        });

        return view('contest.ranking', compact('candidates'));
    }
}