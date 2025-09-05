<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CandidateDetailController extends Controller
{
    public function show($id)
    {
        $candidate = Candidate::with(['user', 'votes.user'])
            ->where('status', 'approved')
            ->findOrFail($id);

        // Compter les votes
        $votesCount = $candidate->votes()->count();

        // Récupérer les votes récents
        $recentVotes = $candidate->votes()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Vérifier si l'utilisateur connecté a déjà voté aujourd'hui
        $hasVotedToday = false;
        if (auth()->check()) {
            $hasVotedToday = $candidate->votes()
                ->where('user_id', auth()->id())
                ->whereDate('created_at', today())
                ->exists();
        }

        // Paramètres du site (mis en cache)
        $settings = Cache::remember('site_settings', 3600, function () {
            return SiteSetting::first();
        });

        return view('candidates.detail', compact(
            'candidate',
            'votesCount',
            'recentVotes',
            'hasVotedToday',
            'settings'
        ));
    }
}

