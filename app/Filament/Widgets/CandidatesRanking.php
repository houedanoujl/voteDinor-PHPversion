<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Candidate;
use Illuminate\Database\Eloquent\Builder;

class CandidatesRanking extends TableWidget
{
    protected static ?string $heading = 'Classement des Candidats';
    protected static ?string $description = 'Top 10 des candidats les plus votés';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Candidate::query()
                    ->where('status', 'approved')
                    ->withCount('votes')
                    ->orderByDesc('votes_count')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('rank')
                    ->label('Rang')
                    ->getStateUsing(function ($record, $loop) {
                        return $loop->iteration;
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1 => 'warning', // Or
                        2 => 'gray',     // Argent
                        3 => 'danger',   // Bronze
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => '🥇 1er',
                        2 => '🥈 2ème',
                        3 => '🥉 3ème',
                        default => $state . 'ème',
                    }),

                ImageColumn::make('photo')
                    ->label('Photo')
                    ->getStateUsing(fn ($record) => $record->getPhotoUrl())
                    ->circular()
                    ->size(50),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('votes_count')
                    ->label('Votes')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    }),
            ])
            ->defaultSort('votes_count', 'desc')
            ->paginated(false);
    }
}
