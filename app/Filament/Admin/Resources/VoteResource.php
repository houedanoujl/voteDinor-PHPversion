<?php

namespace App\Filament\Admin\Resources;

use App\Models\Vote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static ?string $navigationIcon = null;

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
                Tables\Filters\Filter::make('vote_date')
                    ->form([
                        Forms\Components\DatePicker::make('voted_from')
                            ->label('Voté depuis'),
                        Forms\Components\DatePicker::make('voted_until')
                            ->label('Voté jusqu\'à'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['voted_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('vote_date', '>=', $date),
                            )
                            ->when(
                                $data['voted_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('vote_date', '<=', $date),
                            );
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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