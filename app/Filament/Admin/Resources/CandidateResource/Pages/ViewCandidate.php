<?php

namespace App\Filament\Admin\Resources\CandidateResource\Pages;

use App\Filament\Admin\Resources\CandidateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;

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
                        $message = "🎉 Félicitations ! Votre candidature pour le concours photo DINOR a été approuvée.\n\n";
                        $message .= "Votre photo est maintenant visible et vous pouvez recevoir des votes.\n";
                        $message .= "Partagez votre candidature pour obtenir plus de votes !\n\n";
                        $message .= "Bonne chance pour le concours !";
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

    protected function getFormSchema(): array
    {
        return [
            Section::make('Informations personnelles')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Placeholder::make('prenom')
                                ->label('Prénom')
                                ->content(fn ($record) => $record->prenom),
                            Placeholder::make('nom')
                                ->label('Nom')
                                ->content(fn ($record) => $record->nom),
                            Placeholder::make('email')
                                ->label('Email')
                                ->content(fn ($record) => $record->email),
                            Placeholder::make('whatsapp')
                                ->label('WhatsApp')
                                ->content(fn ($record) => $record->whatsapp),
                        ]),
                    Placeholder::make('status')
                        ->label('Statut')
                        ->content(function ($record) {
                            $statusColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ];
                            $color = $statusColors[$record->status] ?? 'gray';
                            $label = $statusLabels[$record->status] ?? $record->status;
                            return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>{$label}</span>";
                        })
                        ->html(),
                ]),

            Section::make('Photo')
                ->schema([
                    Placeholder::make('photo')
                        ->label('Photo')
                        ->content(function ($record) {
                            $photoUrl = $record->getPhotoUrl();
                            return "<img src='{$photoUrl}' alt='Photo de {$record->prenom}' class='w-32 h-32 rounded-full object-cover mx-auto'>";
                        })
                        ->html()
                        ->columnSpanFull(),
                    Placeholder::make('photo_url')
                        ->label('URL de la photo')
                        ->content(fn ($record) => $record->getPhotoUrl())
                        ->columnSpanFull(),
                ]),

            Section::make('Statistiques')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Placeholder::make('votes_count')
                                ->label('Votes reçus')
                                ->content(function ($record) {
                                    return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800'>{$record->votes_count}</span>";
                                })
                                ->html(),
                            Placeholder::make('created_at')
                                ->label('Créé le')
                                ->content(fn ($record) => $record->created_at->format('d/m/Y à H:i')),
                            Placeholder::make('updated_at')
                                ->label('Modifié le')
                                ->content(fn ($record) => $record->updated_at->format('d/m/Y à H:i')),
                        ]),
                ]),

            Section::make('Informations techniques')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Placeholder::make('id')
                                ->label('ID du candidat')
                                ->content(fn ($record) => $record->id),
                            Placeholder::make('user_id')
                                ->label('Utilisateur associé')
                                ->content(fn ($record) => $record->user_id ?? 'Aucun utilisateur'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Votes récents')
                ->schema([
                    Placeholder::make('recent_votes')
                        ->label('Derniers votes')
                        ->content(function ($record) {
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

            Section::make('Statistiques des votes')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Placeholder::make('total_votes')
                                ->label('Total des votes')
                                ->content(fn ($record) => $record->votes()->count()),
                            Placeholder::make('votes_today')
                                ->label('Votes aujourd\'hui')
                                ->content(fn ($record) => $record->votes()->whereDate('created_at', today())->count()),
                            Placeholder::make('votes_this_week')
                                ->label('Votes cette semaine')
                                ->content(fn ($record) => $record->votes()->whereBetween('created_at', [now()->startOfWeek(), now()])->count()),
                        ]),
                ])
                ->collapsible()
                ->collapsed(),
        ];
    }
}
