<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AnalyticsChart;
use App\Filament\Widgets\LatestCandidates;
use App\Filament\Widgets\LatestVotes;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = -2;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AnalyticsChart::class,
            LatestCandidates::class,
            LatestVotes::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard DINOR';
    }

    public function getHeading(): string
    {
        return 'Tableau de bord du concours';
    }

    public function getSubheading(): string
    {
        return 'Vue d\'ensemble des statistiques et du classement';
    }
}
