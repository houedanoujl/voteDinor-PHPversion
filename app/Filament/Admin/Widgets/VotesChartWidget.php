<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Vote;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VotesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Évolution des votes (7 derniers jours)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = Vote::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Votes par jour',
                    'data' => $data,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
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
}