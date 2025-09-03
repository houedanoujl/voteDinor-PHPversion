<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $provider;
    private array $config;

    public function __construct()
    {
        $this->provider = config('services.whatsapp.provider', 'business_api');
        $this->config = config('services.whatsapp');
    }

    /**
     * Envoyer un message WhatsApp
     */
    public function sendMessage(string $phoneNumber, string $message): array
    {
        // Formater le numéro de téléphone pour Green API (sans le +)
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        if ($this->provider === 'business_api') {
            return $this->sendViaBusinessAPI($formattedPhone, $message);
        } else {
            return $this->sendViaGreenAPI($formattedPhone, $message);
        }
    }

    /**
     * Formater le numéro de téléphone pour Green API
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Conserver uniquement les chiffres
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber) ?? '';

        // Règle demandée: pour Green API, envoyer aux 8 derniers chiffres, préfixés par 225
        if ($this->provider === 'green_api') {
            $lastEight = substr($digits, -8);
            return '225' . $lastEight; // Sans + pour Green API
        }

        // Pour WhatsApp Business API, on conserve l'ancien comportement
        if (strlen($digits) === 10) {
            return '225' . $digits;
        }
        if (strpos($digits, '00') === 0) {
            $digits = substr($digits, 2);
        }
        if (strpos($digits, '225') === 0) {
            return $digits;
        }
        return $digits;
    }

    /**
     * Envoyer via WhatsApp Business API
     */
    private function sendViaBusinessAPI(string $phoneNumber, string $message): array
    {
        $config = $this->config['business_api'];
        $phoneNumberId = $config['phone_number_id'];
        $accessToken = $config['access_token'];
        $apiUrl = $config['api_url'];

        if (!$accessToken || !$phoneNumberId) {
            throw new \Exception('Configuration WhatsApp Business API incomplète. Vérifiez WHATSAPP_PHONE_NUMBER_ID et WHATSAPP_ACCESS_TOKEN.');
        }

        $endpoint = "{$apiUrl}/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        Log::info('WhatsApp Business API Response', [
            'phone' => $phoneNumber,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful(),
            'provider' => 'business_api',
        ];
    }

    /**
     * Envoyer via Green API
     */
    private function sendViaGreenAPI(string $phoneNumber, string $message): array
    {
        $config = $this->config['green_api'];
        $instanceId = $config['instance_id'];
        $token = $config['token'];
        $apiUrl = $config['api_url'];

        if (!$instanceId || !$token) {
            throw new \Exception('Configuration Green API incomplète. Vérifiez GREEN_API_ID et GREEN_API_TOKEN.');
        }

        $endpoint = "{$apiUrl}/waInstance{$instanceId}/SendMessage/{$token}";

        $payload = [
            'chatId' => $phoneNumber . '@c.us',
            'message' => $message,
        ];

        Log::info('Green API Request', [
            'endpoint' => $endpoint,
            'phone' => $phoneNumber,
            'payload' => $payload,
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        Log::info('Green API Response', [
            'phone' => $phoneNumber,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful(),
            'provider' => 'green_api',
        ];
    }

    /**
     * Vérifier la configuration
     */
    public function checkConfiguration(): array
    {
        $businessApiConfig = $this->config['business_api'];
        $greenApiConfig = $this->config['green_api'];

        return [
            'business_api' => [
                'configured' => !empty($businessApiConfig['phone_number_id']) && !empty($businessApiConfig['access_token']),
                'phone_number_id' => !empty($businessApiConfig['phone_number_id']),
                'access_token' => !empty($businessApiConfig['access_token']),
            ],
            'green_api' => [
                'configured' => !empty($greenApiConfig['instance_id']) && !empty($greenApiConfig['token']),
                'instance_id' => !empty($greenApiConfig['instance_id']),
                'token' => !empty($greenApiConfig['token']),
            ],
            'current_provider' => $this->provider,
        ];
    }

    /**
     * Construire un message d'approbation
     */
    public function buildApprovalMessage(string $candidateName): string
    {
        return "🎉 Félicitations {$candidateName} !\n\n" .
               "Votre candidature pour le concours photo DINOR a été approuvée !\n\n" .
               "Vous pouvez maintenant recevoir des votes sur notre plateforme.\n\n" .
               "Merci de votre participation !\n\n" .
               "Cordialement,\nDINOR";
    }

    /**
     * Construire un message de rejet
     */
    public function buildRejectionMessage(string $candidateName): string
    {
        return "Bonjour {$candidateName},\n\n" .
               "Nous avons examiné votre candidature pour le concours photo DINOR.\n\n" .
               "Malheureusement, votre candidature n'a pas été retenue cette fois-ci.\n\n" .
               "Nous vous remercions de votre intérêt et vous encourageons à participer à nos futurs concours.\n\n" .
               "Cordialement,\n" .
               "L'équipe DINOR";
    }

    /**
     * Construire un message de test
     */
    public function buildTestMessage(string $candidateName, string $phoneNumber): string
    {
        return "Bonjour {$candidateName},\n\n" .
               "Ceci est un message de test pour vérifier la configuration WhatsApp.\n\n" .
               "Votre numéro: {$phoneNumber}\n\n" .
               "DINOR - Test technique";
    }
}
