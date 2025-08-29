<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Vote;

class LatestVotes extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vote::query()
                    ->with(['candidate', 'user'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('candidate.prenom')
                    ->label('Candidat')
                    ->formatStateUsing(fn ($record) => $record->candidate->prenom . ' ' . $record->candidate->nom)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('candidate.photo')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->candidate->getPhotoUrl()),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Votant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vote_date')
                    ->label('Date du vote')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'valid',
                        'warning' => 'pending',
                        'danger' => 'invalid',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'valid' => 'Valide',
                        'pending' => 'En attente',
                        'invalid' => 'Invalide',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'valid' => 'Valide',
                        'pending' => 'En attente',
                        'invalid' => 'Invalide',
                    ]),
                Tables\Filters\Filter::make('today')
                    ->label('Aujourd\'hui')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('view_candidate')
                    ->label('Voir candidat')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Vote $record): string => route('filament.admin.resources.candidates.view', $record->candidate))
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
