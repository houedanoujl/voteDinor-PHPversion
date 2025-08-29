<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use BackedEnum;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

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
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->getStateUsing(function ($record) {
                        return $record->getPhotoUrl();
                    })
                    ->height(60)
                    ->width(60)
                    ->circular(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->actions([
                Action::make('view')
                    ->label('Détails')
                    ->color('primary')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Candidate $record): string => static::getUrl('view', ['record' => $record])),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->color('success')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->action(function (Candidate $record) {
                        try {
                            $whatsappService = new \App\Services\WhatsAppService();
                            $message = "🎉 Félicitations {$record->prenom} ! Message depuis l'administration DINOR.";
                            $result = $whatsappService->sendMessage($record->whatsapp, $message);

                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Message WhatsApp envoyé avec succès!')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erreur lors de l\'envoi WhatsApp')
                                    ->body($result['message'] ?? 'Erreur inconnue')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erreur WhatsApp')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('approve')
                    ->label('Approuver')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->url(fn (Candidate $record): string => route('admin.candidates.approve', $record))
                    ->openUrlInNewTab(false),

                Action::make('reject')
                    ->label('Rejeter')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Rejeter le candidat')
                    ->modalDescription('Êtes-vous sûr de vouloir rejeter ce candidat ?')
                    ->url(fn (Candidate $record): string => route('admin.candidates.reject', $record))
                    ->openUrlInNewTab(false),

                DeleteAction::make()
                    ->label('Supprimer')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer le candidat')
                    ->modalDescription('Êtes-vous sûr de vouloir supprimer définitivement ce candidat et tous ses votes ?')
                    ->action(function (Candidate $record) {
                        try {
                            $votesCount = $record->votes()->count();
                            $record->votes()->delete();
                            $candidateName = $record->prenom . ' ' . $record->nom;
                            $record->delete();

                            \Filament\Notifications\Notification::make()
                                ->title('Candidat supprimé avec succès!')
                                ->body("'{$candidateName}' et {$votesCount} votes supprimés.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erreur lors de la suppression')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('votes')
            ->with('user');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CandidateResource\Pages\ListCandidates::route('/'),
            'view' => \App\Filament\Admin\Resources\CandidateResource\Pages\ViewCandidate::route('/{record}'),
        ];
    }
}
