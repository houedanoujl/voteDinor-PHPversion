<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppAdmin extends Command
{
    protected $signature = 'test:whatsapp-admin';
    protected $description = 'Test l\'envoi WhatsApp vers l\'admin';

    public function handle()
    {
        $this->info('=== Test configuration WhatsApp Admin ===');

        // 1. Vérifier la config
        $adminPhone = config('services.whatsapp.admin_phone');
        $provider = config('services.whatsapp.provider');

        $this->info("Admin phone: {$adminPhone}");
        $this->info("Provider: {$provider}");

        if (empty($adminPhone)) {
            $this->error('❌ ADMIN_WHATSAPP non configuré');
            return 1;
        }

        // 2. Vérifier la config du provider
        $service = new WhatsAppService();
        $config = $service->checkConfiguration();

        $this->info("Configuration:");
        $this->line(json_encode($config, JSON_PRETTY_PRINT));

        if (!$config['business_api']['configured'] && !$config['green_api']['configured']) {
            $this->error('❌ Aucun provider WhatsApp configuré');
            return 1;
        }

        // 3. Test d'envoi
        $this->info('📱 Test d\'envoi vers admin...');
        $message = "🧪 Test notification admin DINOR\n\nTest depuis commande Artisan\nHeure: " . now()->format('H:i:s');

        try {
            $result = $service->sendMessage($adminPhone, $message);

            $this->info("Résultat:");
            $this->line(json_encode($result, JSON_PRETTY_PRINT));

            if ($result['success']) {
                $this->info('✅ Message envoyé avec succès!');
                return 0;
            } else {
                $this->error('❌ Échec envoi: ' . ($result['body']['error'] ?? 'Erreur inconnue'));
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            return 1;
        }
    }
}
