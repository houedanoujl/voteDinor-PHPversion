<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AnalyticsChart extends Widget
{
    public function getView(): string
    {
        return 'filament.widgets.analytics-chart';
    }

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'analyticsData' => $this->getAnalyticsData(),
            'contestStats' => $this->getContestStats(),
            'topCandidates' => $this->getTopCandidates(),
        ];
    }

    private function getAnalyticsData(): array
    {
        // Simulation des données Google Analytics
        // En production, vous utiliseriez l'API Google Analytics
        $days = collect(range(7, 0))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('Y-m-d');
        });

        return [
            'visitors' => $days->map(function ($date) {
                return [
                    'date' => Carbon::parse($date)->format('d/m'),
                    'value' => rand(50, 200),
                ];
            })->toArray(),
            'pageViews' => $days->map(function ($date) {
                return [
                    'date' => Carbon::parse($date)->format('d/m'),
                    'value' => rand(100, 400),
                ];
            })->toArray(),
            'votes' => $days->map(function ($date) {
                return [
                    'date' => Carbon::parse($date)->format('d/m'),
                    'value' => rand(10, 50),
                ];
            })->toArray(),
        ];
    }

    private function getContestStats(): array
    {
        $totalCandidates = \App\Models\Candidate::count();
        $approvedCandidates = \App\Models\Candidate::where('status', 'approved')->count();
        $totalVotes = \App\Models\Vote::count();
        $votesToday = \App\Models\Vote::whereDate('created_at', today())->count();

        return [
            'total_candidates' => $totalCandidates,
            'approved_candidates' => $approvedCandidates,
            'total_votes' => $totalVotes,
            'votes_today' => $votesToday,
        ];
    }

    private function getTopCandidates(): array
    {
        return \App\Models\Candidate::where('status', 'approved')
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($candidate, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $candidate->prenom . ' ' . $candidate->nom,
                    'votes' => $candidate->votes_count,
                    'photo' => $candidate->getPhotoUrl(),
                    'medal' => $this->getMedal($index + 1),
                ];
            })
            ->toArray();
    }

    private function getMedal(int $rank): string
    {
        return match ($rank) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => '',
        };
    }
}
