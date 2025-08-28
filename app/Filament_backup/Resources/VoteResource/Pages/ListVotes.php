<?php

namespace App\Filament\Resources\VoteResource\Pages;

use App\Filament\Resources\VoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVotes extends ListRecords
{
    protected static string $resource = VoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Pas de création manuelle de votes
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous les votes'),
            
            'today' => Tab::make("Aujourd'hui")
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('vote_date', today()))
                ->badge(fn () => \App\Models\Vote::whereDate('vote_date', today())->count()),
            
            'this_week' => Tab::make('Cette semaine')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('vote_date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]))
                ->badge(fn () => \App\Models\Vote::whereBetween('vote_date', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ])->count()),
            
            'this_month' => Tab::make('Ce mois')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('vote_date', now()->month))
                ->badge(fn () => \App\Models\Vote::whereMonth('vote_date', now()->month)->count()),
        ];
    }
}