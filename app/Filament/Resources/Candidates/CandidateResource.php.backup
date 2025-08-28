<?php

namespace App\Filament\Resources\Candidates;

use App\Filament\Resources\Candidates\Pages\CreateCandidate;
use App\Filament\Resources\Candidates\Pages\EditCandidate;
use App\Filament\Resources\Candidates\Pages\ListCandidates;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use App\Mail\CandidateApproved;
use Illuminate\Support\Facades\Mail;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';
    
    protected static ?string $pluralModelLabel = 'Candidats';
    
    protected static ?string $modelLabel = 'Candidat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du candidat')
                    ->schema([
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Photo et statut')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_url')
                            ->label('Photo')
                            ->image()
                            ->directory('candidates')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Components\TextInput::make('votes_count')
                            ->label('Nombre de votes')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->circular()
                    ->size(60),
                
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn ($record) => $record->prenom . ' ' . $record->nom)
                    ->searchable(['prenom', 'nom'])
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable()
                    ->icon('heroicon-m-phone'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ]),
                
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Candidate $record) => $record->status === 'pending')
                    ->action(function (Candidate $record) {
                        $record->approve();
                        
                        // Envoyer email de notification
                        if ($record->email) {
                            Mail::send(new CandidateApproved($record));
                        }
                        
                        // Notification Filament
                        \Filament\Notifications\Notification::make()
                            ->title('Candidat approuvé')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Candidate $record) => $record->status === 'pending')
                    ->action(function (Candidate $record) {
                        $record->reject();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Candidat rejeté')
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCandidates::route('/'),
            'create' => CreateCandidate::route('/create'),
            'edit' => EditCandidate::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}