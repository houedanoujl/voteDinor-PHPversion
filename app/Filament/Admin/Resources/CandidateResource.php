<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
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
                    ])->columns(2),
                
                Forms\Components\Section::make('Description et statut')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(500),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\TextInput::make('votes_count')
                            ->label('Nombre de votes')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->action(function (Candidate $record) {
                        $record->update(['status' => 'approved']);
                        
                        // Envoyer le message WhatsApp
                        try {
                            $whatsappService = new WhatsAppService();
                            $message = "🎉 Félicitations ! Votre candidature pour le concours photo DINOR a été approuvée. Vous pouvez maintenant recevoir des votes. Bonne chance !";
                            $whatsappService->sendMessage($record->whatsapp, $message);
                        } catch (\Exception $e) {
                            \Log::error('Erreur WhatsApp: ' . $e->getMessage());
                        }
                        
                        Notification::make()
                            ->title('Candidat approuvé')
                            ->body('Le candidat a été approuvé et un message WhatsApp a été envoyé.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Candidate $record) {
                        $record->update(['status' => 'rejected']);
                        
                        Notification::make()
                            ->title('Candidat rejeté')
                            ->body('Le candidat a été rejeté.')
                            ->success()
                            ->send();
                    }),
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
            ->withCount('votes');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CandidateResource\Pages\ListCandidates::route('/'),
            'create' => \App\Filament\Admin\Resources\CandidateResource\Pages\CreateCandidate::route('/create'),
            'edit' => \App\Filament\Admin\Resources\CandidateResource\Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}