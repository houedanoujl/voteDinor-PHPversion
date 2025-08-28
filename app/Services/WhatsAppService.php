<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private string $instanceId;
    private string $token;

    public function __construct()
    {
        $this->instanceId = config('services.whatsapp.instance_id');
        $this->token = config('services.whatsapp.token');
        $this->apiUrl = "https://api.green-api.com/waInstance{$this->instanceId}";
    }

    /**
     * Envoyer un message texte WhatsApp
     */
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp service non configuré');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage/{$this->token}", [
                'chatId' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info("Message WhatsApp envoyé à {$phoneNumber}");
                return true;
            }

            Log::error("Erreur envoi WhatsApp: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Exception envoi WhatsApp: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer une notification d'approbation de candidature
     */
    public function sendCandidateApprovalNotification(string $phoneNumber, string $candidateName): bool
    {
        $message = "🎉 Félicitations {$candidateName} !\n\n";
        $message .= "Votre candidature au Concours Photo Rétro DINOR a été approuvée !\n\n";
        $message .= "Votre photo participe maintenant officiellement au concours.\n";
        $message .= "Les visiteurs peuvent voter pour vous.\n\n";
        $message .= "🔗 Voir le concours : " . route('home') . "\n\n";
        $message .= "Bonne chance ! 🍀\n";
        $message .= "L'équipe DINOR";

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envoyer une notification de nouveau vote
     */
    public function sendVoteNotification(string $phoneNumber, string $candidateName, int $totalVotes): bool
    {
        $message = "🗳️ Nouveau vote pour {$candidateName} !\n\n";
        $message .= "Vous avez maintenant {$totalVotes} vote(s) au total.\n\n";
        $message .= "Continuez à partager votre participation !\n";
        $message .= "L'équipe DINOR";

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envoyer un résumé quotidien des votes
     */
    public function sendDailySummary(string $phoneNumber, string $candidateName, int $todayVotes, int $totalVotes): bool
    {
        $message = "📊 Résumé quotidien - {$candidateName}\n\n";
        $message .= "Votes aujourd'hui : {$todayVotes}\n";
        $message .= "Total votes : {$totalVotes}\n\n";
        $message .= "🔗 Voir le classement : " . route('home') . "\n\n";
        $message .= "L'équipe DINOR";

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Vérifier si le service est configuré
     */
    private function isConfigured(): bool
    {
        return !empty($this->instanceId) && !empty($this->token);
    }

    /**
     * Formater le numéro de téléphone pour l'API
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les caractères non numériques sauf le +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Si le numéro commence par +225, le conserver tel quel
        if (str_starts_with($cleaned, '+225')) {
            return str_replace('+', '', $cleaned) . '@c.us';
        }
        
        // Si le numéro commence par 225, ajouter le +
        if (str_starts_with($cleaned, '225')) {
            return $cleaned . '@c.us';
        }
        
        // Si le numéro ne commence ni par +225 ni par 225, l'ajouter
        return '225' . $cleaned . '@c.us';
    }

    /**
     * Vérifier le statut de l'instance WhatsApp
     */
    public function checkStatus(): array
    {
        if (!$this->isConfigured()) {
            return ['status' => 'not_configured'];
        }

        try {
            $response = Http::get("{$this->apiUrl}/getStateInstance/{$this->token}");
            
            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'error', 'message' => $response->body()];
        } catch (\Exception $e) {
            return ['status' => 'exception', 'message' => $e->getMessage()];
        }
    }
}