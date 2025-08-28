<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppTestPage extends Page implements HasForms
{
    use InteractsWithForms;



    protected static ?string $navigationLabel = 'Test WhatsApp';

    protected static ?string $title = 'Test des Envois WhatsApp';

    protected static ?string $slug = 'whatsapp-test';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Configuration du Test')
                    ->description('Testez l\'envoi de messages WhatsApp via l\'API')
                    ->schema([
                        $this->getConfigurationInfo(),

                        Select::make('provider')
                            ->label('Provider WhatsApp')
                            ->options([
                                'business_api' => 'WhatsApp Business API (Meta)',
                                'green_api' => 'Green API',
                            ])
                            ->default('business_api')
                            ->required()
                            ->reactive(),

                        Select::make('test_type')
                            ->label('Type de Test')
                            ->options([
                                'approval' => 'Message d\'approbation candidat',
                                'rejection' => 'Message de rejet candidat',
                                'custom' => 'Message personnalisé',
                            ])
                            ->default('approval')
                            ->required()
                            ->reactive(),

                        TextInput::make('phone_number')
                            ->label('Numéro WhatsApp')
                            ->tel()
                            ->placeholder('+2250701234567')
                            ->required()
                            ->helperText('Format international: +225XXXXXXXXX'),

                        TextInput::make('candidate_name')
                            ->label('Nom du candidat (pour les tests)')
                            ->placeholder('Jean Dupont')
                            ->visible(fn ($get) => in_array($get('test_type'), ['approval', 'rejection'])),

                        Textarea::make('custom_message')
                            ->label('Message personnalisé')
                            ->rows(4)
                            ->placeholder('Entrez votre message personnalisé...')
                            ->visible(fn ($get) => $get('test_type') === 'custom'),
                    ])
                    ->columns(2),

                Section::make('Résultats du Test')
                    ->schema([
                        Textarea::make('test_result')
                            ->label('Résultat')
                            ->rows(6)
                            ->disabled()
                            ->placeholder('Le résultat du test apparaîtra ici...'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->id('results-section'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_test')
                ->label('📱 Envoyer Test WhatsApp')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action('sendWhatsAppTest')
                ->requiresConfirmation()
                ->modalHeading('Confirmer l\'envoi du test WhatsApp')
                ->modalDescription('Êtes-vous sûr de vouloir envoyer ce message de test ?')
                ->modalSubmitActionLabel('Envoyer le test'),
        ];
    }

    public function sendWhatsAppTest(): void
    {
        $data = $this->form->getState();

        try {
            $message = $this->buildMessage($data);
            $response = $this->sendWhatsAppMessage($data['phone_number'], $message, $data['provider']);

            // Afficher le résultat
            $this->data['test_result'] = $this->formatResponse($response);

            // Notification de succès
            Notification::make()
                ->title('Test WhatsApp envoyé')
                ->success()
                ->body('Le message de test a été envoyé avec succès.')
                ->send();

        } catch (\Exception $e) {
            $this->data['test_result'] = "❌ Erreur: " . $e->getMessage();

            Notification::make()
                ->title('Erreur lors de l\'envoi')
                ->danger()
                ->body('Impossible d\'envoyer le message de test.')
                ->send();
        }

        // Ouvrir la section des résultats
        $this->dispatch('open-section', 'results-section');
    }

    private function buildMessage(array $data): string
    {
        $candidateName = $data['candidate_name'] ?? 'Test Candidat';

        switch ($data['test_type']) {
            case 'approval':
                return "🎉 Félicitations {$candidateName} !\n\nVotre candidature pour le concours photo DINOR a été approuvée !\n\nVous pouvez maintenant recevoir des votes sur notre plateforme.\n\nMerci de votre participation !\n\nDINOR - Cuisine Vintage";

            case 'rejection':
                return "Bonjour {$candidateName},\n\nNous avons examiné votre candidature pour le concours photo DINOR.\n\nMalheureusement, votre candidature n'a pas été retenue cette fois-ci.\n\nNous vous remercions de votre intérêt et vous encourageons à participer à nos futurs concours.\n\nCordialement,\nL'équipe DINOR";

            case 'custom':
                return $data['custom_message'] ?? 'Message de test DINOR';

            default:
                return 'Message de test DINOR';
        }
    }

    private function sendWhatsAppMessage(string $phoneNumber, string $message, string $provider): array
    {
        if ($provider === 'business_api') {
            return $this->sendViaBusinessAPI($phoneNumber, $message);
        } else {
            return $this->sendViaGreenAPI($phoneNumber, $message);
        }
    }

    private function sendViaBusinessAPI(string $phoneNumber, string $message): array
    {
        $config = config('services.whatsapp.business_api');
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

    private function sendViaGreenAPI(string $phoneNumber, string $message): array
    {
        $config = config('services.whatsapp.green_api');
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

    private function formatResponse(array $response): string
    {
        $status = $response['status'];
        $body = $response['body'];
        $success = $response['success'];
        $provider = $response['provider'] ?? 'unknown';

        $result = "📊 Statut de la réponse: {$status}\n";
        $result .= "🔧 Provider utilisé: {$provider}\n";
        $result .= $success ? "✅ Succès\n" : "❌ Échec\n\n";
        $result .= "📋 Détails de la réponse:\n";
        $result .= json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $result;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('clear')
                ->label('Effacer')
                ->color('gray')
                ->action(function () {
                    $this->form->fill();
                    $this->data['test_result'] = '';
                }),
        ];
    }

    private function getConfigurationInfo()
    {
        $businessApiConfig = config('services.whatsapp.business_api');
        $greenApiConfig = config('services.whatsapp.green_api');

        $businessApiStatus = $businessApiConfig['phone_number_id'] && $businessApiConfig['access_token'] ? '✅' : '❌';
        $greenApiStatus = $greenApiConfig['instance_id'] && $greenApiConfig['token'] ? '✅' : '❌';

        $content = "
        <div class='space-y-4 p-4 bg-gray-50 rounded-lg border'>
            <h4 class='font-semibold text-gray-900 mb-3'>Configuration WhatsApp</h4>

            <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                <div class='bg-white p-3 rounded border'>
                    <div class='flex items-center justify-between mb-2'>
                        <h5 class='font-medium text-gray-800'>WhatsApp Business API</h5>
                        <span class='text-lg'>{$businessApiStatus}</span>
                    </div>
                    <div class='space-y-1 text-sm'>
                        <div class='flex justify-between'>
                            <span class='text-gray-600'>Phone Number ID:</span>
                            <span class='font-mono text-xs'>" . ($businessApiConfig['phone_number_id'] ? 'Configuré' : 'Non configuré') . "</span>
                        </div>
                        <div class='flex justify-between'>
                            <span class='text-gray-600'>Access Token:</span>
                            <span class='font-mono text-xs'>" . ($businessApiConfig['access_token'] ? 'Configuré' : 'Non configuré') . "</span>
                        </div>
                    </div>
                </div>

                <div class='bg-white p-3 rounded border'>
                    <div class='flex items-center justify-between mb-2'>
                        <h5 class='font-medium text-gray-800'>Green API</h5>
                        <span class='text-lg'>{$greenApiStatus}</span>
                    </div>
                    <div class='space-y-1 text-sm'>
                        <div class='flex justify-between'>
                            <span class='text-gray-600'>Instance ID:</span>
                            <span class='font-mono text-xs'>" . ($greenApiConfig['instance_id'] ? 'Configuré' : 'Non configuré') . "</span>
                        </div>
                        <div class='flex justify-between'>
                            <span class='text-gray-600'>Token:</span>
                            <span class='font-mono text-xs'>" . ($greenApiConfig['token'] ? 'Configuré' : 'Non configuré') . "</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class='mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800'>
                <strong>💡 Instructions:</strong><br>
                • Configurez au moins un provider pour tester les envois WhatsApp<br>
                • WhatsApp Business API nécessite un compte Business vérifié<br>
                • Green API est une alternative plus simple à configurer
            </div>
        </div>";

        return \Filament\Forms\Components\Placeholder::make('config_info')
            ->label('État de la Configuration')
            ->content($content)
            ->columnSpanFull();
    }
}
