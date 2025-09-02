<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first();
        $stats = [
            'total_candidates' => Candidate::approved()->count(),
            'total_votes' => Vote::count(),
            'top_candidates' => Candidate::approved()
                ->orderBy('votes_count', 'desc')
                ->take(3)
                ->get()
        ];

        return view('contest.home', [
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    public function ranking()
    {
        $candidates = Candidate::approved()
            ->orderBy('votes_count', 'desc')
            ->get();

        return view('contest.ranking', compact('candidates'));
    }
}