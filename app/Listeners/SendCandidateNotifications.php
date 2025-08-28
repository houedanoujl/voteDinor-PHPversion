<?php

namespace App\Listeners;

use App\Events\CandidateRegisteredEvent;
use App\Mail\CandidateRegistered;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCandidateNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private WhatsAppService $whatsAppService
    ) {}

    /**
     * Gérer l'événement d'inscription de candidat
     */
    public function handle(CandidateRegisteredEvent $event): void
    {
        $candidate = $event->candidate;

        try {
            // 1. Envoyer un email à l'admin
            Mail::send(new CandidateRegistered($candidate));
            
            Log::info("Email d'inscription candidat envoyé pour: {$candidate->full_name}");

            // 2. Envoyer une confirmation WhatsApp au candidat (optionnel)
            if ($candidate->whatsapp) {
                $message = "🎉 Inscription reçue !\n\n";
                $message .= "Bonjour {$candidate->prenom},\n\n";
                $message .= "Votre inscription au Concours Photo DINOR a été enregistrée !\n";
                $message .= "Votre candidature sera examinée sous 24h.\n\n";
                $message .= "L'équipe DINOR";

                $this->whatsAppService->sendMessage($candidate->whatsapp, $message);
                
                Log::info("WhatsApp de confirmation envoyé à: {$candidate->whatsapp}");
            }

            // 3. Logger pour le suivi
            Log::info("Notifications envoyées pour candidat #{$candidate->id}: {$candidate->full_name}");

        } catch (\Exception $e) {
            // En cas d'erreur, logger mais ne pas faire échouer le job
            Log::error("Erreur envoi notifications candidat #{$candidate->id}: " . $e->getMessage());
            
            // Optionnel: Re-lancer le job après délai
            $this->release(60); // Retry après 1 minute
        }
    }

    /**
     * Gérer l'échec du job
     */
    public function failed(CandidateRegisteredEvent $event, \Throwable $exception): void
    {
        Log::error("Échec définitif envoi notifications candidat #{$event->candidate->id}: " . $exception->getMessage());
        
        // Optionnel: Envoyer une alerte à l'admin
        // Mail::to('admin@dinor.ci')->send(new NotificationFailureAlert($event->candidate, $exception));
    }
}