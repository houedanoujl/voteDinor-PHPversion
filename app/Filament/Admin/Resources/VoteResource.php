<?php

namespace App\Filament\Admin\Resources;

use App\Models\Vote;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static ?string $navigationLabel = 'Votes';

    protected static ?string $pluralModelLabel = 'Votes';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->label('Candidat')
                    ->sortable()
                    ->searchable(['candidates.prenom', 'candidates.nom']),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Anonyme'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Adresse IP')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('vote_date')
                    ->label('Date de vote')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Voté le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('candidate')
                    ->relationship('candidate', 'nom')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),
                Tables\Filters\SelectFilter::make('vote_period')
                    ->label('Période de vote')
                    ->options([
                        'today' => 'Aujourd\'hui',
                        'yesterday' => 'Hier',
                        'this_week' => 'Cette semaine',
                        'last_week' => 'Semaine dernière',
                        'this_month' => 'Ce mois',
                        'last_month' => 'Mois dernier',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $query, string $period) {
                            return match ($period) {
                                'today' => $query->whereDate('created_at', today()),
                                'yesterday' => $query->whereDate('created_at', today()->subDay()),
                                'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                                'last_week' => $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]),
                                'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                                'last_month' => $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
                                default => $query,
                            };
                        });
                    }),
                Tables\Filters\TernaryFilter::make('user_id')
                    ->label('Avec utilisateur')
                    ->placeholder('Tous')
                    ->trueLabel('Avec utilisateur')
                    ->falseLabel('Anonymes')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('user_id'),
                        false: fn (Builder $query) => $query->whereNull('user_id'),
                    ),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['candidate', 'user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\VoteResource\Pages\ListVotes::route('/'),
        ];
    }
}