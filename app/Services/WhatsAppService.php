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
        if ($this->provider === 'business_api') {
            return $this->sendViaBusinessAPI($phoneNumber, $message);
        } else {
            return $this->sendViaGreenAPI($phoneNumber, $message);
        }
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
               "DINOR - Cuisine Vintage";
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