<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Carbon\Carbon;

class DetailedStats extends Widget
{
    public function getView(): string
    {
        return 'filament.widgets.detailed-stats';
    }

    protected static ?string $heading = 'Statistiques Détaillées';
    protected static ?string $description = 'Analyses approfondies du concours';

    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $startOfWeek = $now->startOfWeek();

        return [
            'general' => [
                'total_candidates' => Candidate::count(),
                'approved_candidates' => Candidate::where('status', 'approved')->count(),
                'pending_candidates' => Candidate::where('status', 'pending')->count(),
                'rejected_candidates' => Candidate::where('status', 'rejected')->count(),
                'total_votes' => Vote::count(),
                'total_users' => User::count(),
            ],
            'periods' => [
                'votes_today' => Vote::whereDate('created_at', today())->count(),
                'votes_this_week' => Vote::whereBetween('created_at', [$startOfWeek, $now])->count(),
                'votes_this_month' => Vote::whereBetween('created_at', [$startOfMonth, $now])->count(),
                'users_today' => User::whereDate('created_at', today())->count(),
                'users_this_week' => User::whereBetween('created_at', [$startOfWeek, $now])->count(),
                'users_this_month' => User::whereBetween('created_at', [$startOfMonth, $now])->count(),
            ],
            'averages' => [
                'avg_votes_per_candidate' => Candidate::where('status', 'approved')->avg('votes_count') ?? 0,
                'avg_votes_per_day' => Vote::whereBetween('created_at', [$now->subDays(7), $now])->count() / 7,
                'participation_rate' => $this->calculateParticipationRate(),
            ],
            'top_performers' => [
                'most_voted' => Candidate::where('status', 'approved')
                    ->withCount('votes')
                    ->orderByDesc('votes_count')
                    ->first(),
                'recent_activity' => Vote::with('candidate', 'user')
                    ->latest()
                    ->take(5)
                    ->get(),
            ]
        ];
    }

    private function calculateParticipationRate(): float
    {
        $totalUsers = User::count();
        $usersWhoVoted = User::whereHas('votes')->count();

        return $totalUsers > 0 ? round(($usersWhoVoted / $totalUsers) * 100, 2) : 0;
    }
}
