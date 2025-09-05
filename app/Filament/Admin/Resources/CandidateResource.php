<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use BackedEnum;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->getStateUsing(function ($record) {
                        return $record->getPhotoUrl();
                    })
                    ->height(60)
                    ->width(60)
                    ->circular(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->actions([
                Action::make('view')
                    ->label('Détails')
                    ->color('primary')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Candidate $record): string => static::getUrl('view', ['record' => $record])),

                Action::make('notifyCandidate')
                    ->label('Notifier le candidat')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('reason')
                            ->label('Raison')
                            ->options([
                                'missing' => 'Photo manquante',
                                'blurry' => 'Photo floue',
                                'inappropriate' => 'Photo inappropriée',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, Candidate $record) {
                                $message = match ($state) {
                                    'missing' => "Bonjour {$record->prenom},\n\nNous avons constaté que votre candidature n\'inclut pas de photo.\nMerci d\'ajouter une photo nette de vous afin de valider votre participation au concours DINOR.\n\nLien d\'upload (si disponible) : https://votedinor.com/ajouter-photo\n\nCordialement,\nL\'équipe DINOR",
                                    'blurry' => "Bonjour {$record->prenom},\n\nVotre photo semble floue et ne permet pas une bonne identification.\nMerci de soumettre une nouvelle photo nette pour valider votre participation.\n\nCritères : visage bien visible, photo claire et lumineuse.\n\nCordialement,\nL\'équipe DINOR",
                                    'inappropriate' => "Bonjour {$record->prenom},\n\nVotre photo ne respecte pas nos règles de participation (contenu inapproprié).\nMerci de soumettre une photo conforme aux règles : photo personnelle, respectueuse et appropriée.\n\nCordialement,\nL\'équipe DINOR",
                                    default => "Bonjour {$record->prenom},\n\nMessage concernant votre candidature au concours DINOR.",
                                };
                                $set('message', $message);
                            }),
                        Forms\Components\Textarea::make('message')
                            ->label('Message WhatsApp')
                            ->rows(10)
                            ->required(),
                    ])
                    ->action(function (array $data, Candidate $record) {
                        try {
                            $service = new WhatsAppService();
                            $result = $service->sendMessage($record->whatsapp, $data['message']);

                            if (!empty($result['success'])) {
                                Notification::make()
                                    ->title('Message envoyé au candidat')
                                    ->success()
                                    ->body('Le message WhatsApp a été envoyé avec succès.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Échec de l\'envoi du message')
                                    ->danger()
                                    ->body(($result['message'] ?? 'Erreur inconnue') . ' [' . ($result['provider'] ?? 'whatsapp') . ']')
                                    ->send();
                            }
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Erreur WhatsApp')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->color('success')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->action(function (Candidate $record) {
                        try {
                            $whatsappService = new \App\Services\WhatsAppService();
                            $message = "🎉 Félicitations {$record->prenom} ! Message depuis l'administration DINOR.";
                            $result = $whatsappService->sendMessage($record->whatsapp, $message);

                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Message WhatsApp envoyé avec succès!')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erreur lors de l\'envoi WhatsApp')
                                    ->body($result['message'] ?? 'Erreur inconnue')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erreur WhatsApp')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('approve')
                    ->label('Approuver')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->url(fn (Candidate $record): string => route('admin.candidates.approve', $record))
                    ->openUrlInNewTab(false),

                Action::make('reject')
                    ->label('Rejeter')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Candidate $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Rejeter le candidat')
                    ->modalDescription('Êtes-vous sûr de vouloir rejeter ce candidat ?')
                    ->url(fn (Candidate $record): string => route('admin.candidates.reject', $record))
                    ->openUrlInNewTab(false),

                DeleteAction::make()
                    ->label('Supprimer')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer le candidat')
                    ->modalDescription('Êtes-vous sûr de vouloir supprimer définitivement ce candidat et tous ses votes ?')
                    ->action(function (Candidate $record) {
                        try {
                            $votesCount = $record->votes()->count();
                            $record->votes()->delete();
                            $candidateName = $record->prenom . ' ' . $record->nom;
                            $record->delete();

                            \Filament\Notifications\Notification::make()
                                ->title('Candidat supprimé avec succès!')
                                ->body("'{$candidateName}' et {$votesCount} votes supprimés.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erreur lors de la suppression')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('convertHeic')
                    ->label('🍎 Convertir HEIC')
                    ->icon('heroicon-o-photo')
                    ->color('warning')
                    ->form([
                        Forms\Components\Toggle::make('backup')
                            ->label('Sauvegarder les fichiers originaux')
                            ->default(true)
                            ->helperText('Les fichiers HEIC originaux seront conservés dans un dossier backup'),
                        Forms\Components\Toggle::make('update_db')
                            ->label('Mettre à jour la base de données')
                            ->default(true)
                            ->helperText('Met à jour automatiquement les références dans la base de données'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $command = 'candidates:convert-heic';
                            $parameters = [];
                            
                            if ($data['backup']) {
                                $parameters['--backup'] = true;
                            }
                            if ($data['update_db']) {
                                $parameters['--update-db'] = true;
                            }
                            
                            $exitCode = \Illuminate\Support\Facades\Artisan::call($command, $parameters);
                            $output = \Illuminate\Support\Facades\Artisan::output();
                            
                            \Log::info('HEIC Conversion Output:', ['output' => $output, 'exitCode' => $exitCode]);
                            
                            // Parser le résultat pour extraire les statistiques
                            preg_match('/✅ Convertis.*?(\d+)/', $output, $converted);
                            preg_match('/❌ Erreurs.*?(\d+)/', $output, $errors);
                            
                            $convertedCount = isset($converted[1]) ? (int)$converted[1] : 0;
                            $errorCount = isset($errors[1]) ? (int)$errors[1] : 0;
                            
                            if ($exitCode !== 0) {
                                throw new \Exception("Commande échouée (code {$exitCode}): " . strip_tags($output));
                            }
                            
                            if ($convertedCount > 0) {
                                Notification::make()
                                    ->title('Conversion HEIC terminée !')
                                    ->body("✅ {$convertedCount} fichier(s) converti(s) • ❌ {$errorCount} erreur(s)")
                                    ->success()
                                    ->duration(10000)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Aucun fichier HEIC trouvé')
                                    ->body('Pas de conversion nécessaire')
                                    ->info()
                                    ->send();
                            }
                            
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Erreur lors de la conversion HEIC')
                                ->body($e->getMessage())
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    })
                    ->modalHeading('🍎 Conversion HEIC → JPEG (Anti-iOS)')
                    ->modalDescription('Convertit automatiquement tous les fichiers HEIC uploadés par les utilisateurs iOS en format JPEG standard.')
                    ->modalSubmitActionLabel('Lancer la conversion')
                    ->requiresConfirmation(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('votes')
            ->with('user');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CandidateResource\Pages\ListCandidates::route('/'),
            'view' => \App\Filament\Admin\Resources\CandidateResource\Pages\ViewCandidate::route('/{record}'),
        ];
    }
}
