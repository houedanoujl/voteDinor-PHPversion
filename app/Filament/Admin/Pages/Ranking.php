<?php

namespace App\Filament\Admin\Pages;

use App\Models\Candidate;
use Filament\Pages\Page;

class Ranking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static string $view = 'filament.admin.pages.ranking';
    protected static ?string $title = 'Classement des candidats';
    protected static ?int $navigationSort = 4;

    public function getViewData(): array
    {
        return [
            'candidates' => Candidate::approved()
                ->withCount('votes')
                ->orderBy('votes_count', 'desc')
                ->get(),
        ];
    }
}