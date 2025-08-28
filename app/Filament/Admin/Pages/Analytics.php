<?php

namespace App\Filament\Admin\Pages;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Filament\Pages\Page;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.admin.pages.analytics';
    protected static ?string $title = 'Analytics & Statistiques';
    protected static ?int $navigationSort = 3;

    public function getViewData(): array
    {
        return [
            'totalCandidates' => Candidate::count(),
            'approvedCandidates' => Candidate::approved()->count(),
            'totalVotes' => Vote::count(),
            'totalUsers' => User::count(),
            'votesLastWeek' => Vote::where('created_at', '>=', now()->subWeek())->count(),
            'topCandidates' => Candidate::approved()
                ->orderBy('votes_count', 'desc')
                ->take(5)
                ->get(),
            'recentVotes' => Vote::with(['candidate', 'user'])
                ->latest()
                ->take(10)
                ->get(),
            'votesPerDay' => Vote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }
}