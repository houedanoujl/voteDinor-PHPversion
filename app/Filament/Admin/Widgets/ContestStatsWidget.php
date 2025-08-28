<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContestStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCandidates = Candidate::count();
        $approvedCandidates = Candidate::approved()->count();
        $totalVotes = Vote::count();
        $totalUsers = User::count();
        $votesToday = Vote::whereDate('created_at', today())->count();
        $topCandidate = Candidate::approved()->orderBy('votes_count', 'desc')->first();

        return [
            Stat::make('Total des candidats', $totalCandidates)
                ->description($approvedCandidates . ' approuvés')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Total des votes', $totalVotes)
                ->description($votesToday . ' aujourd\'hui')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),
            
            Stat::make('Utilisateurs inscrits', $totalUsers)
                ->description('Total des comptes')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            
            Stat::make('Candidat en tête', $topCandidate?->full_name ?? 'Aucun')
                ->description(($topCandidate?->votes_count ?? 0) . ' votes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}