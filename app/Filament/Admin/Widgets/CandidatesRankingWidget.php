<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Candidate;
use Filament\Widgets\Widget;

class CandidatesRankingWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public static function getView(): string
    {
        return 'filament.admin.widgets.candidates-ranking';
    }

    public function getCandidatesRanking()
    {
        return Candidate::where('status', 'approved')
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getStats()
    {
        return [
            'totalVotes' => \App\Models\Vote::count(),
            'totalCandidates' => Candidate::count(),
            'approvedCandidates' => Candidate::where('status', 'approved')->count(),
            'pendingCandidates' => Candidate::where('status', 'pending')->count(),
        ];
    }
}
