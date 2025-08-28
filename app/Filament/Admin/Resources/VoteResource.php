<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VoteResource\Pages;
use App\Models\Vote;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Votes';
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->label('Candidat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de vote')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('candidate')
                    ->relationship('candidate', 'nom')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('today')
                    ->label('Aujourd\'hui')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVotes::route('/'),
            'view' => Pages\ViewVote::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}