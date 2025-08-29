<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class GoogleAnalytics extends Page
{
    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Google Analytics Dashboard';

    protected static ?int $navigationSort = 11;

    public function getView(): string
    {
        return 'filament.admin.pages.google-analytics';
    }

    public function getStats(): array
    {
        // En production, vous devriez utiliser l'API Google Analytics
        // Pour maintenant, nous simulons des données
        return [
            'total_visitors' => $this->getTotalVisitors(),
            'page_views' => $this->getPageViews(),
            'bounce_rate' => $this->getBounceRate(),
            'avg_session_duration' => $this->getAvgSessionDuration(),
            'top_pages' => $this->getTopPages(),
            'traffic_sources' => $this->getTrafficSources(),
        ];
    }

    protected function getTotalVisitors(): int
    {
        // Simulation - remplacer par l'API Google Analytics
        return rand(1500, 3000);
    }

    protected function getPageViews(): int
    {
        // Simulation - remplacer par l'API Google Analytics
        return rand(5000, 10000);
    }

    protected function getBounceRate(): string
    {
        // Simulation - remplacer par l'API Google Analytics
        return rand(20, 60) . '%';
    }

    protected function getAvgSessionDuration(): string
    {
        // Simulation - remplacer par l'API Google Analytics
        $minutes = rand(2, 8);
        $seconds = rand(10, 59);
        return $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    protected function getTopPages(): array
    {
        return [
            ['page' => '/', 'views' => rand(800, 1500), 'percentage' => rand(25, 40) . '%'],
            ['page' => '/dashboard', 'views' => rand(400, 800), 'percentage' => rand(15, 25) . '%'],
            ['page' => '/classement', 'views' => rand(300, 600), 'percentage' => rand(10, 20) . '%'],
            ['page' => '/login', 'views' => rand(200, 400), 'percentage' => rand(5, 15) . '%'],
            ['page' => '/register', 'views' => rand(100, 300), 'percentage' => rand(3, 10) . '%'],
        ];
    }

    protected function getTrafficSources(): array
    {
        return [
            ['source' => 'Recherche Google', 'visitors' => rand(500, 1000), 'percentage' => rand(30, 50) . '%'],
            ['source' => 'Direct', 'visitors' => rand(300, 600), 'percentage' => rand(20, 30) . '%'],
            ['source' => 'Réseaux sociaux', 'visitors' => rand(200, 400), 'percentage' => rand(15, 25) . '%'],
            ['source' => 'Référence', 'visitors' => rand(100, 200), 'percentage' => rand(5, 15) . '%'],
            ['source' => 'Email', 'visitors' => rand(50, 150), 'percentage' => rand(3, 10) . '%'],
        ];
    }

    protected function getVotingStats(): array
    {
        // Récupérer les vraies statistiques de vote depuis la base de données
        $totalVotes = \App\Models\Vote::count();
        $votesToday = \App\Models\Vote::whereDate('vote_date', today())->count();
        $uniqueVoters = \App\Models\Vote::distinct('ip_address')->count();
        $topCandidate = \App\Models\Candidate::orderBy('votes_count', 'desc')->first();

        return [
            'total_votes' => $totalVotes,
            'votes_today' => $votesToday,
            'unique_voters' => $uniqueVoters,
            'top_candidate' => $topCandidate ? $topCandidate->full_name : 'Aucun',
            'top_candidate_votes' => $topCandidate ? $topCandidate->votes_count : 0,
        ];
    }

    public function getCandidateStats(): array
    {
        return [
            'total_candidates' => \App\Models\Candidate::count(),
            'pending_candidates' => \App\Models\Candidate::where('status', 'pending')->count(),
            'approved_candidates' => \App\Models\Candidate::where('status', 'approved')->count(),
            'rejected_candidates' => \App\Models\Candidate::where('status', 'rejected')->count(),
        ];
    }

    public function getUserStats(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'users_today' => \App\Models\User::whereDate('created_at', today())->count(),
            'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
            'social_users' => 0, // Temporarily disabled until provider column is properly set up
        ];
    }
}