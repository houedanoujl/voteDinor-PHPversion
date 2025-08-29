<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class VotingStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculer les statistiques en une seule requête
        $totalVotes = Vote::count();
        $totalCandidates = Candidate::count();
        $approvedCandidates = Candidate::where('status', 'approved')->count();
        $totalUsers = User::count();

        // Candidat le plus voté
        $topCandidate = Candidate::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->first();

        // Votes aujourd'hui
        $votesToday = Vote::whereDate('created_at', today())->count();

        // Votes cette semaine
        $votesThisWeek = Vote::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        return [
            Stat::make('Total Votes', number_format($totalVotes))
                ->description('Votes enregistrés')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Candidats Approuvés', $approvedCandidates . '/' . $totalCandidates)
                ->description('Candidats validés')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->chart([3, 5, 7, 2, 4, 6, 8, 1]),

            Stat::make('Votes Aujourd\'hui', number_format($votesToday))
                ->description('Votes du jour')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 4, 3, 7, 5, 6, 4, 8]),

            Stat::make('Leader Actuel', $topCandidate ? $topCandidate->prenom . ' ' . $topCandidate->nom : 'Aucun')
                ->description($topCandidate ? number_format($topCandidate->votes_count) . ' votes' : 'Pas de votes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('danger')
                ->chart([1, 3, 5, 7, 9, 8, 6, 4]),
        ];
    }
}
