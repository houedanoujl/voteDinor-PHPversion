<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Utilisateurs';
    
    protected static ?string $pluralModelLabel = 'Utilisateurs';
    
    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations utilisateur')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('google_id')
                            ->label('Google ID')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('facebook_id')
                            ->label('Facebook ID')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('avatar')
                            ->label('Avatar URL')
                            ->url()
                            ->maxLength(255),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Inscrit le')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder-avatar.svg'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Vérifié')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\BadgeColumn::make('oauth_provider')
                    ->label('Connexion')
                    ->getStateUsing(function ($record) {
                        if ($record->google_id) return 'Google';
                        if ($record->facebook_id) return 'Facebook';
                        return 'Email';
                    })
                    ->colors([
                        'primary' => 'Google',
                        'info' => 'Facebook', 
                        'gray' => 'Email',
                    ]),
                
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->counts('votes')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('candidates_count')
                    ->label('Candidatures')
                    ->counts('candidates')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label('Utilisateurs vérifiés')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at'))
                    ->toggle(),
                
                Tables\Filters\SelectFilter::make('oauth_provider')
                    ->label('Méthode de connexion')
                    ->options([
                        'google' => 'Google',
                        'facebook' => 'Facebook',
                        'email' => 'Email',
                    ])
                    ->query(function ($query, $value) {
                        return match ($value) {
                            'google' => $query->whereNotNull('google_id'),
                            'facebook' => $query->whereNotNull('facebook_id'),
                            'email' => $query->whereNull('google_id')->whereNull('facebook_id'),
                            default => $query,
                        };
                    }),
                
                Tables\Filters\Filter::make('has_votes')
                    ->label('Ont voté')
                    ->query(fn ($query) => $query->has('votes'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('has_candidates')
                    ->label('Ont candidaté')
                    ->query(fn ($query) => $query->has('candidates'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $todayCount = static::getNavigationBadge();
        return $todayCount > 0 ? 'info' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'view' => \App\Filament\Resources\UserResource\Pages\ViewUser::route('/{record}'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}