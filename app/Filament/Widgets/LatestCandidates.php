<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Candidate;
use Illuminate\Database\Eloquent\Builder;

class LatestCandidates extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Candidate::query()
                    ->where('status', 'approved')
                    ->withCount('votes')
                    ->orderBy('votes_count', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rang')
                    ->getStateUsing(function ($record, $loop) {
                        return '#' . ($loop->iteration);
                    })
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        str_contains($state, '#1') => 'success',
                        str_contains($state, '#2') => 'warning',
                        str_contains($state, '#3') => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getPhotoUrl()),

                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->counts('votes')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Candidate $record): string => route('filament.admin.resources.candidates.view', $record))
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('votes_count', 'desc')
            ->paginated([10, 25, 50]);
    }
}
