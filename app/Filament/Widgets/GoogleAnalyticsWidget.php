<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleAnalyticsWidget extends Widget
{
    public function getView(): string
    {
        return 'filament.widgets.google-analytics';
    }
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        return [
            'analyticsData' => $this->getGoogleAnalyticsData(),
            'realTimeData' => $this->getRealTimeData(),
            'topPages' => $this->getTopPages(),
            'deviceStats' => $this->getDeviceStats(),
            'trafficSources' => $this->getTrafficSources(),
            'geoData' => $this->getGeoData(),
            'isConfigured' => $this->isGoogleAnalyticsConfigured(),
        ];
    }

    private function isGoogleAnalyticsConfigured(): bool
    {
        return !empty(config('services.google_analytics.tracking_id')) || 
               !empty(config('services.google_analytics.measurement_id'));
    }

    private function getGoogleAnalyticsData(): array
    {
        if (!$this->isGoogleAnalyticsConfigured()) {
            return $this->getFallbackData();
        }

        // Cache les données pour éviter trop d'appels à l'API
        return Cache::remember('google_analytics_data', 300, function () {
            try {
                // En production, vous utiliseriez l'API Google Analytics 4
                // Pour l'instant, on simule des données réalistes
                return $this->getSimulatedAnalyticsData();
            } catch (\Exception $e) {
                Log::error('Erreur Google Analytics: ' . $e->getMessage());
                return $this->getFallbackData();
            }
        });
    }

    private function getSimulatedAnalyticsData(): array
    {
        $days = collect(range(29, 0))->map(function ($day) {
            return Carbon::now()->subDays($day);
        });

        return [
            'sessions' => $days->map(function ($date) {
                $baseValue = rand(80, 250);
                // Simuler plus de trafic le week-end
                if ($date->isWeekend()) {
                    $baseValue = (int)($baseValue * 1.3);
                }
                return [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => $baseValue,
                ];
            })->toArray(),
            
            'users' => $days->map(function ($date) {
                $baseValue = rand(60, 180);
                if ($date->isWeekend()) {
                    $baseValue = (int)($baseValue * 1.2);
                }
                return [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => $baseValue,
                ];
            })->toArray(),
            
            'pageViews' => $days->map(function ($date) {
                $baseValue = rand(200, 800);
                if ($date->isWeekend()) {
                    $baseValue = (int)($baseValue * 1.4);
                }
                return [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => $baseValue,
                ];
            })->toArray(),
            
            'bounceRate' => $days->map(function ($date) {
                return [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => rand(35, 65),
                ];
            })->toArray(),
        ];
    }

    private function getRealTimeData(): array
    {
        return [
            'active_users' => rand(15, 45),
            'active_sessions' => rand(20, 60),
            'page_views_per_minute' => rand(5, 20),
        ];
    }

    private function getTopPages(): array
    {
        return [
            ['page' => '/', 'title' => 'Accueil - Concours DINOR', 'views' => rand(800, 1500), 'percentage' => rand(25, 35)],
            ['page' => '/classement', 'title' => 'Classement des candidats', 'views' => rand(600, 1200), 'percentage' => rand(20, 30)],
            ['page' => '/candidat/*', 'title' => 'Pages candidats', 'views' => rand(400, 800), 'percentage' => rand(15, 25)],
            ['page' => '/regles', 'title' => 'Règles du concours', 'views' => rand(200, 400), 'percentage' => rand(8, 15)],
            ['page' => '/login', 'title' => 'Connexion', 'views' => rand(150, 300), 'percentage' => rand(5, 12)],
        ];
    }

    private function getDeviceStats(): array
    {
        return [
            'desktop' => rand(45, 65),
            'mobile' => rand(25, 45),
            'tablet' => rand(5, 15),
        ];
    }

    private function getTrafficSources(): array
    {
        return [
            ['source' => 'Recherche organique', 'percentage' => rand(35, 55), 'color' => '#10B981'],
            ['source' => 'Réseaux sociaux', 'percentage' => rand(20, 35), 'color' => '#3B82F6'],
            ['source' => 'Accès direct', 'percentage' => rand(15, 25), 'color' => '#8B5CF6'],
            ['source' => 'Référents', 'percentage' => rand(5, 15), 'color' => '#F59E0B'],
            ['source' => 'Email', 'percentage' => rand(2, 8), 'color' => '#EF4444'],
        ];
    }

    private function getGeoData(): array
    {
        return [
            ['country' => 'Côte d\'Ivoire', 'users' => rand(800, 1500), 'percentage' => rand(70, 85)],
            ['country' => 'France', 'users' => rand(50, 150), 'percentage' => rand(5, 12)],
            ['country' => 'États-Unis', 'users' => rand(20, 80), 'percentage' => rand(2, 8)],
            ['country' => 'Canada', 'users' => rand(15, 60), 'percentage' => rand(1, 5)],
            ['country' => 'Autres', 'users' => rand(30, 100), 'percentage' => rand(3, 10)],
        ];
    }

    private function getFallbackData(): array
    {
        return [
            'sessions' => collect(range(7, 0))->map(function ($day) {
                $date = Carbon::now()->subDays($day);
                return [
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('d/m'),
                    'value' => 0,
                ];
            })->toArray(),
            'users' => [],
            'pageViews' => [],
            'bounceRate' => [],
        ];
    }
}