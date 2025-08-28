<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_candidates' => Candidate::approved()->count(),
            'total_votes' => Vote::count(),
            'top_candidates' => Candidate::approved()
                ->orderBy('votes_count', 'desc')
                ->take(3)
                ->get()
        ];

        return view('contest.home', compact('stats'));
    }

    public function ranking()
    {
        $candidates = Candidate::approved()
            ->orderBy('votes_count', 'desc')
            ->get();

        return view('contest.ranking', compact('candidates'));
    }
}