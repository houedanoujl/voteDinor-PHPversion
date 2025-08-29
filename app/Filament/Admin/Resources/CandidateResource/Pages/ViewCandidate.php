<?php

namespace App\Filament\Admin\Resources\CandidateResource\Pages;

use App\Filament\Admin\Resources\CandidateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;

class ViewCandidate extends ViewRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

    public function getTitle(): string
    {
        return 'Détails du candidat';
    }

    public function getHeading(): string
    {
        return $this->record->prenom . ' ' . $this->record->nom;
    }

    public function getSubheading(): string
    {
        return 'ID: ' . $this->record->id . ' • ' . 
               ($this->record->status === 'approved' ? '✅ Approuvé' : 
                ($this->record->status === 'rejected' ? '❌ Rejeté' : '⏳ En attente'));
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'votesToday' => $this->record->votes()->whereDate('created_at', today())->count(),
            'votesWeek' => $this->record->votes()->whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'votesMonth' => $this->record->votes()->whereBetween('created_at', [now()->startOfMonth(), now()])->count(),
            'totalVotes' => $this->record->votes()->count(),
            'recentVotes' => $this->record->votes()->with('user')->latest()->take(10)->get(),
        ];
    }

    public function getView(): string
    {
        return 'filament.admin.pages.view-candidate';
    }
}