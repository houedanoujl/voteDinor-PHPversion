<?php

namespace App\Filament\Admin\Resources\Candidates;

use App\Filament\Admin\Resources\Candidates\Pages\CreateCandidate;
use App\Filament\Admin\Resources\Candidates\Pages\EditCandidate;
use App\Filament\Admin\Resources\Candidates\Pages\ListCandidates;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;

use App\Mail\CandidateApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

    protected static ?string $modelLabel = 'Candidat';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Informations du candidat')
                    ->schema([
                        Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),

                        Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\Section::make('Photo et statut')
                    ->schema([
                        Components\FileUpload::make('photo_url')
                            ->label('Photo')
                            ->image()
                            ->directory('candidates')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),

                        Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ])
                            ->default('pending')
                            ->required(),

                        Components\TextInput::make('votes_count')
                            ->label('Nombre de votes')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->circular()
                    ->size(60),

                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn ($record) => $record->prenom . ' ' . $record->nom)
                    ->searchable(['prenom', 'nom'])
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable()
                    ->icon('heroicon-m-phone'),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ]),

                TextColumn::make('votes_count')
                    ->label('Votes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ])
            ->actions([
                EditAction::make(),

                Action::make('test_whatsapp')
                    ->label('Test WhatsApp')
                    ->icon('heroicon-o-chat-bubble')
                    ->color('info')
                    ->action(function (Candidate $record) {
                        return self::testWhatsAppForCandidate($record);
                    })
                    ->modalHeading('Test WhatsApp pour ' . fn($record) => $record->prenom . ' ' . $record->nom)
                    ->modalDescription('Envoyer un message de test WhatsApp à ce candidat')
                    ->modalSubmitActionLabel('Envoyer le test')
                    ->requiresConfirmation(),

                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Candidate $record) => $record->status === 'pending')
                    ->action(function (Candidate $record) {
                        $record->approve();

                        // Envoyer email de notification
                        if ($record->email) {
                            Mail::send(new CandidateApproved($record));
                        }

                        // Notification Filament
                        \Filament\Notifications\Notification::make()
                            ->title('Candidat approuvé')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Candidate $record) => $record->status === 'pending')
                    ->action(function (Candidate $record) {
                        $record->reject();

                        \Filament\Notifications\Notification::make()
                            ->title('Candidat rejeté')
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCandidates::route('/'),
            'create' => CreateCandidate::route('/create'),
            'edit' => EditCandidate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }

    public static function testWhatsAppForCandidate(Candidate $candidate): void
    {
        try {
            $message = "Bonjour {$candidate->prenom},\n\nCeci est un message de test pour vérifier la configuration WhatsApp.\n\nVotre numéro: {$candidate->whatsapp}\n\nDINOR - Test technique";

            $response = self::sendWhatsAppMessage($candidate->whatsapp, $message);

            if ($response['success']) {
                \Filament\Notifications\Notification::make()
                    ->title('Test WhatsApp envoyé')
                    ->success()
                    ->body("Message de test envoyé à {$candidate->prenom} ({$candidate->whatsapp})")
                    ->send();
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Erreur WhatsApp')
                    ->danger()
                    ->body("Impossible d'envoyer le message: " . json_encode($response['body']))
                    ->send();
            }

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Erreur WhatsApp')
                ->danger()
                ->body("Exception: " . $e->getMessage())
                ->send();
        }
    }

    private static function sendWhatsAppMessage(string $phoneNumber, string $message): array
    {
        $provider = config('services.whatsapp.provider', 'business_api');

        if ($provider === 'business_api') {
            return self::sendViaBusinessAPI($phoneNumber, $message);
        } else {
            return self::sendViaGreenAPI($phoneNumber, $message);
        }
    }

    private static function sendViaBusinessAPI(string $phoneNumber, string $message): array
    {
        $config = config('services.whatsapp.business_api');
        $phoneNumberId = $config['phone_number_id'];
        $accessToken = $config['access_token'];
        $apiUrl = $config['api_url'];

        if (!$accessToken || !$phoneNumberId) {
            throw new \Exception('Configuration WhatsApp Business API incomplète');
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

        Log::info('WhatsApp Business API Test', [
            'phone' => $phoneNumber,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful(),
        ];
    }

    private static function sendViaGreenAPI(string $phoneNumber, string $message): array
    {
        $config = config('services.whatsapp.green_api');
        $instanceId = $config['instance_id'];
        $token = $config['token'];
        $apiUrl = $config['api_url'];

        if (!$instanceId || !$token) {
            throw new \Exception('Configuration Green API incomplète');
        }

        $endpoint = "{$apiUrl}/waInstance{$instanceId}/SendMessage/{$token}";

        $payload = [
            'chatId' => $phoneNumber . '@c.us',
            'message' => $message,
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        Log::info('Green API Test', [
            'phone' => $phoneNumber,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful(),
        ];
    }
}
