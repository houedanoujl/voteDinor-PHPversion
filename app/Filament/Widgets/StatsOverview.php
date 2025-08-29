<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCandidates = Candidate::count();
        $approvedCandidates = Candidate::where('status', 'approved')->count();
        $pendingCandidates = Candidate::where('status', 'pending')->count();
        $totalVotes = Vote::count();
        $votesToday = Vote::whereDate('created_at', today())->count();
        $totalUsers = User::count();
        $usersToday = User::whereDate('created_at', today())->count();

        return [
            Stat::make('Total Candidats', $totalCandidates)
                ->description('Tous les candidats inscrits')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Candidats Approuvés', $approvedCandidates)
                ->description('Candidats validés et en lice')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 5, 8, 12, 15, 18, 20]),

            Stat::make('En Attente', $pendingCandidates)
                ->description('Candidats en cours de validation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 3, 1, 4, 2, 1, 3]),

            Stat::make('Total Votes', $totalVotes)
                ->description('Votes cumulés')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger')
                ->chart([10, 15, 25, 30, 45, 60, 75]),

            Stat::make('Votes Aujourd\'hui', $votesToday)
                ->description('Votes du jour')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 22, 25]),

            Stat::make('Utilisateurs Inscrits', $totalUsers)
                ->description('Total des comptes créés')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, 12, 14]),
        ];
    }
}
