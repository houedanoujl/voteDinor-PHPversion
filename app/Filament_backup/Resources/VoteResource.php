<?php

namespace App\Filament\Resources;

use App\Models\Vote;
use App\Models\Candidate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static ?string $navigationLabel = 'Votes';
    
    protected static ?string $pluralModelLabel = 'Votes';
    
    protected static ?string $modelLabel = 'Vote';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du vote')
                    ->schema([
                        Forms\Components\Select::make('candidate_id')
                            ->label('Candidat')
                            ->relationship('candidate', 'prenom')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->prenom . ' ' . $record->nom)
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Utilisateur')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Adresse IP')
                            ->disabled(),
                        
                        Forms\Components\DatePicker::make('vote_date')
                            ->label('Date du vote')
                            ->required(),
                        
                        Forms\Components\Textarea::make('user_agent')
                            ->label('Navigateur')
                            ->disabled()
                            ->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->label('Candidat')
                    ->getStateUsing(fn ($record) => $record->candidate->prenom . ' ' . $record->candidate->nom)
                    ->searchable(['candidate.prenom', 'candidate.nom'])
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable()
                    ->fontFamily('mono'),
                
                Tables\Columns\TextColumn::make('vote_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Voté le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Navigateur')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->fontFamily('mono'),
            ])
            ->filters([
                SelectFilter::make('candidate')
                    ->label('Candidat')
                    ->relationship('candidate', 'prenom')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->prenom . ' ' . $record->nom),
                
                SelectFilter::make('user')
                    ->label('Utilisateur')
                    ->relationship('user', 'name'),
                
                Filter::make('vote_date')
                    ->label('Période')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('vote_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('vote_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Du ' . \Carbon\Carbon::parse($data['from'])->toDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Au ' . \Carbon\Carbon::parse($data['until'])->toDateString();
                        }
                        return $indicators;
                    }),
                
                Filter::make('today')
                    ->label("Votes d'aujourd'hui")
                    ->query(fn (Builder $query): Builder => $query->whereDate('vote_date', today()))
                    ->toggle(),
                
                Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('vote_date', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString()
                    ]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Actualisation automatique toutes les 30s
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $todayCount = static::getNavigationBadge();
        return $todayCount > 0 ? 'success' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\VoteResource\Pages\ListVotes::route('/'),
            'view' => \App\Filament\Resources\VoteResource\Pages\ViewVote::route('/{record}'),
            'edit' => \App\Filament\Resources\VoteResource\Pages\EditVote::route('/{record}/edit'),
        ];
    }
}