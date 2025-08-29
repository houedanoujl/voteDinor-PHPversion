<?php

namespace App\Filament\Admin\Resources\CandidateResource\Pages;

use App\Filament\Admin\Resources\CandidateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;

class ViewCandidate extends ViewRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifier'),
            Action::make('approve')
                ->label('Approuver')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->update(['status' => 'approved']);

                    // Envoyer le message WhatsApp
                    try {
                        $whatsappService = new WhatsAppService();
                        $message = "🎉 Félicitations ! Votre candidature pour le concours photo DINOR a été approuvée. Vous pouvez maintenant recevoir des votes. Bonne chance !";
                        $whatsappService->sendMessage($this->record->whatsapp, $message);
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
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Rejeter le candidat')
                ->modalDescription('Êtes-vous sûr de vouloir rejeter ce candidat ?')
                ->modalSubmitActionLabel('Rejeter')
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);

                    Notification::make()
                        ->title('Candidat rejeté')
                        ->body('Le candidat a été rejeté.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Section::make('Informations personnelles')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('prenom')
                                    ->label('Prénom'),
                                TextEntry::make('nom')
                                    ->label('Nom'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('whatsapp')
                                    ->label('WhatsApp'),
                            ]),
                        TextEntry::make('status')
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
                    ]),

                Section::make('Photo et description')
                    ->schema([
                        ImageEntry::make('photo')
                            ->label('Photo')
                            ->getStateUsing(fn ($record) => $record->getPhotoUrl())
                            ->circular(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->default('Aucune description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Statistiques')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('votes_count')
                                    ->label('Votes reçus')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('created_at')
                                    ->label('Créé le')
                                    ->dateTime('d/m/Y à H:i'),
                                TextEntry::make('updated_at')
                                    ->label('Modifié le')
                                    ->dateTime('d/m/Y à H:i'),
                            ]),
                    ]),

                Section::make('Informations techniques')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID du candidat'),
                                TextEntry::make('user_id')
                                    ->label('Utilisateur associé')
                                    ->default('Aucun utilisateur'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Votes récents')
                    ->schema([
                        TextEntry::make('recent_votes')
                            ->label('Derniers votes')
                            ->getStateUsing(function ($record) {
                                $recentVotes = $record->votes()->with('user')->latest()->take(5)->get();

                                if ($recentVotes->isEmpty()) {
                                    return 'Aucun vote reçu';
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($recentVotes as $vote) {
                                    $userName = $vote->user ? $vote->user->name : 'Visiteur';
                                    $html .= "<div class='flex justify-between items-center p-2 bg-gray-50 rounded'>";
                                    $html .= "<span class='text-sm'>{$userName}</span>";
                                    $html .= "<span class='text-xs text-gray-500'>{$vote->created_at->format('d/m/Y H:i')}</span>";
                                    $html .= "</div>";
                                }
                                $html .= '</div>';

                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
