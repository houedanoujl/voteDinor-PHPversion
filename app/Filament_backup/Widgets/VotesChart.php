<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VotesChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des votes (7 derniers jours)';

    protected function getData(): array
    {
        $data = Vote::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Créer un tableau avec tous les jours (même ceux sans votes)
        $labels = [];
        $values = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $vote = $data->firstWhere('date', $date->toDateString());
            $values[] = $vote ? $vote->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Votes par jour',
                    'data' => $values,
                    'backgroundColor' => 'rgba(255, 140, 0, 0.2)',
                    'borderColor' => 'rgba(255, 140, 0, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}