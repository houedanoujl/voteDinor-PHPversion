<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Candidats approuvés', Candidate::where('status', 'approved')->count())
                ->description('Total des candidats validés')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('En attente', Candidate::where('status', 'pending')->count())
                ->description('Candidats en attente de validation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Votes aujourd\'hui', Vote::whereDate('created_at', today())->count())
                ->description('Votes enregistrés aujourd\'hui')
                ->descriptionIcon('heroicon-m-heart')
                ->color('primary'),
            
            Stat::make('Total votes', Vote::count())
                ->description('Tous les votes depuis le début')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
            
            Stat::make('Utilisateurs inscrits', User::count())
                ->description('Total des utilisateurs OAuth')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
            
            Stat::make('Candidat leader', $this->getLeadingCandidate())
                ->description($this->getLeadingCandidateVotes() . ' votes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
    
    private function getLeadingCandidate(): string
    {
        $candidate = Candidate::approved()
            ->orderBy('votes_count', 'desc')
            ->first();
            
        return $candidate ? $candidate->prenom . ' ' . substr($candidate->nom, 0, 1) . '.' : 'Aucun';
    }
    
    private function getLeadingCandidateVotes(): int
    {
        $candidate = Candidate::approved()
            ->orderBy('votes_count', 'desc')
            ->first();
            
        return $candidate ? $candidate->votes_count : 0;
    }
}