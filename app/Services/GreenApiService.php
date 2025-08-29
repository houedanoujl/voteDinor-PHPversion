<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GreenApiService
{
    private string $apiUrl;
    private string $instanceId;
    private string $accessToken;

    public function __construct()
    {
        $this->instanceId = config('services.whatsapp.green_api.instance_id');
        $this->accessToken = config('services.whatsapp.green_api.token');
        $this->apiUrl = "https://api.green-api.com/waInstance{$this->instanceId}";
    }

    /**
     * Envoie un message WhatsApp via Green API
     */
    public function sendMessage(string $phoneNumber, string $message): array
    {
        try {
            // Nettoyer le numéro de téléphone
            $cleanPhone = $this->cleanPhoneNumber($phoneNumber);

            $url = "{$this->apiUrl}/sendMessage/{$this->accessToken}";

            $payload = [
                'chatId' => $cleanPhone . '@c.us',
                'message' => $message
            ];

            Log::info('Envoi message WhatsApp via Green API', [
                'phone' => $cleanPhone,
                'message' => $message,
                'url' => $url
            ]);

            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('Message WhatsApp envoyé avec succès', [
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'Message envoyé avec succès',
                    'data' => $responseData
                ];
            } else {
                Log::error('Erreur lors de l\'envoi du message WhatsApp', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi: ' . $response->status(),
                    'error' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi WhatsApp', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Envoie un message avec fichier (image, document, etc.)
     */
    public function sendFileByUrl(string $phoneNumber, string $fileUrl, string $fileName = null, string $caption = null): array
    {
        try {
            $cleanPhone = $this->cleanPhoneNumber($phoneNumber);

            $url = "{$this->apiUrl}/sendFileByUrl/{$this->accessToken}";

            $payload = [
                'chatId' => $cleanPhone . '@c.us',
                'urlFile' => $fileUrl,
                'fileName' => $fileName ?? basename($fileUrl),
                'caption' => $caption ?? ''
            ];

            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Fichier envoyé avec succès',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du fichier',
                    'error' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi de fichier WhatsApp', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifie le statut de l'instance
     */
    public function getStateInstance(): array
    {
        try {
            $url = "{$this->apiUrl}/getStateInstance/{$this->accessToken}";

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la vérification du statut'
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Nettoie et formate le numéro de téléphone
     */
    private function cleanPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les caractères non numériques sauf le +
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Si le numéro commence par +, le garder
        // Sinon, ajouter le préfixe +225 pour la Côte d'Ivoire
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+225' . $cleaned;
        }

        // Supprimer le + pour Green API (format requis)
        return ltrim($cleaned, '+');
    }

    /**
     * Génère un message de bienvenue pour un candidat
     */
    public function generateWelcomeMessage(string $prenom, string $nom): string
    {
        return "🎉 Bonjour {$prenom} {$nom},\n\n" .
               "Félicitations ! Votre candidature pour le concours photo DINOR a été approuvée avec succès.\n\n" .
               "✅ Vous pouvez maintenant recevoir des votes\n" .
               "🌟 Partagez votre participation avec vos proches\n" .
               "🏆 Bonne chance pour le concours !\n\n" .
               "L'équipe DINOR vous souhaite le meilleur ! 🚀";
    }

    /**
     * Génère un message de notification pour un candidat
     */
    public function generateNotificationMessage(string $prenom, string $nom, string $status): string
    {
        if ($status === 'approved') {
            return $this->generateWelcomeMessage($prenom, $nom);
        } elseif ($status === 'rejected') {
            return "Bonjour {$prenom} {$nom},\n\n" .
                   "Nous vous remercions pour votre intérêt au concours photo DINOR.\n\n" .
                   "Malheureusement, votre candidature n'a pas pu être retenue cette fois-ci.\n\n" .
                   "Nous vous encourageons à participer lors de nos prochains événements.\n\n" .
                   "Cordialement,\n" .
                   "L'équipe DINOR";
        }

        return "Bonjour {$prenom} {$nom},\n\n" .
               "Nous avons bien reçu votre candidature pour le concours photo DINOR.\n\n" .
               "Votre dossier est en cours d'examen. Nous vous tiendrons informé de la suite.\n\n" .
               "Merci pour votre participation !\n\n" .
               "L'équipe DINOR";
    }
}
