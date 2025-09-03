<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Mail\CandidateRegistered; // Placeholder if you want email; can be replaced with dedicated mail
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOnUserRegistered implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    public function handle(UserRegisteredEvent $event): void
    {
        $user = $event->user;

        try {
            // WhatsApp admin
            $adminPhone = config('services.whatsapp.admin_phone');
            if (!empty($adminPhone)) {
                $userUrl = route('filament.admin.resources.users.edit', $user);
                $message = "🔔 Nouvel utilisateur inscrit\n\n" .
                    "Nom: {$user->name}\n" .
                    (!empty($user->whatsapp) ? "WhatsApp: {$user->whatsapp}\n" : '') .
                    (!empty($user->email) ? "Email: {$user->email}\n" : '') .
                    "ID utilisateur: {$user->id}\n" .
                    "Méthode: {$event->method}\n\n" .
                    "▶ Utilisateur: {$userUrl}";
                $this->whatsAppService->sendMessage($adminPhone, $message);
            }

            // Optionnel: envoi email admin si MAIL configuré
            if (config('mail.default')) {
                try {
                    Mail::raw(
                        "Nouvel utilisateur inscrit: {$user->name} (ID #{$user->id})\n" .
                        ("Email: " . ($user->email ?: 'n/a') . "\n") .
                        ("WhatsApp: " . ($user->whatsapp ?: 'n/a') . "\n") .
                        "Méthode: {$event->method}",
                        function ($msg) {
                            $msg->to(config('mail.from.address'))
                                ->subject('Nouvelle inscription utilisateur');
                        }
                    );
                } catch (\Throwable $e) {
                    Log::warning('Email admin non envoyé: ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::error('Erreur notification admin (inscription utilisateur): ' . $e->getMessage());
        }
    }
}


