<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

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
                Tables\Columns\TextColumn::make('id')
                    ->label('Actions')
                    ->formatStateUsing(function ($state, $record) {
                                                $cleanWhatsapp = preg_replace('/[^\d+]/', '', $record->whatsapp);
                        if (!str_starts_with($cleanWhatsapp, '+')) {
                            $cleanWhatsapp = '+225' . $cleanWhatsapp;
                        }
                        $message = "Bonjour {$record->prenom} {$record->nom}, nous avons bien reçu votre candidature pour le concours DINOR. Nous vous tiendrons informé de la suite.";
                        $whatsappUrl = "https://wa.me/{$cleanWhatsapp}?text=" . urlencode($message);

                                                                        $whatsappButton = "<button onclick='sendWhatsAppMessage({$record->id})' 
                            class='inline-flex items-center gap-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-green-400'
                            title='Envoyer un message WhatsApp via Green API'>
                            <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 24 24'>
                                <path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.786z'/>
                            </svg>
                            WhatsApp
                        </button>";

                                                // Bouton de détail
                        $viewUrl = route('filament.admin.resources.candidates.view', $record);
                        $detailButton = "<a href='{$viewUrl}' 
                            class='inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 shadow-sm hover:shadow-lg transform hover:-translate-y-0.5'
                            title='Voir les détails du candidat'>
                            <svg class='w-3 h-3' fill='currentColor' viewBox='0 0 20 20'>
                                <path d='M10 12a2 2 0 100-4 2 2 0 000 4z'/>
                                <path fill-rule='evenodd' d='M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z' clip-rule='evenodd'/>
                            </svg>
                            Détail
                        </a>";

                        if ($record->status === 'pending') {
                            $approveUrl = route('admin.candidates.approve', $record);
                            $rejectUrl = route('admin.candidates.reject', $record);
                            return "
                            <div class='flex flex-col gap-2 min-w-max'>
                                <div class='flex gap-1 flex-wrap'>
                                    <a href='{$approveUrl}' 
                                       class='inline-flex items-center gap-1 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-emerald-400'
                                       title='Approuver le candidat'>
                                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'>
                                            <path fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/>
                                        </svg>
                                        Approuver
                                    </a>
                                    <a href='{$rejectUrl}' 
                                       onclick='return confirm(\"Êtes-vous sûr de vouloir rejeter ce candidat ?\")' 
                                       class='inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-red-400'
                                       title='Rejeter le candidat'>
                                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'>
                                            <path fill-rule='evenodd' d='M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z' clip-rule='evenodd'/>
                                        </svg>
                                        Rejeter
                                    </a>
                                </div>
                                <div class='flex gap-1 flex-wrap'>
                                    {$detailButton}
                                    {$whatsappButton}
                                </div>
                                <div class='flex gap-1'>
                                    {$deleteButton}
                                </div>
                            </div>";
                        }

                        $statusBadge = match ($record->status) {
                            'approved' => '<span class="text-green-600 font-medium text-xs">✓ Approuvé</span>',
                            'rejected' => '<span class="text-red-600 font-medium text-xs">✗ Rejeté</span>',
                            default => '<span class="text-gray-600 text-xs">-</span>'
                        };

                                                                        $deleteButton = "<form method='POST' action='/admin/candidates/{$record->id}' style='display: inline;' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce candidat ?\")'>
                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                            <input type='hidden' name='_method' value='DELETE'>
                            <button type='submit' class='inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 shadow-sm hover:shadow-md'
                                title='Supprimer le candidat'>
                                <svg class='w-3 h-3' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z' clip-rule='evenodd' />
                                </svg>
                                Supprimer
                            </button>
                        </form>";

                        return "
                        <div class='flex flex-col gap-2 min-w-max'>
                            <div class='flex items-center gap-2'>
                                {$statusBadge}
                            </div>
                            <div class='flex gap-1 flex-wrap'>
                                {$detailButton}
                                {$whatsappButton}
                            </div>
                            <div class='flex gap-1'>
                                {$deleteButton}
                            </div>
                        </div>";
                    })
                    ->html(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null) // Désactive le clic sur la ligne
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
            ->withCount('votes');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CandidateResource\Pages\ListCandidates::route('/'),
            'view' => \App\Filament\Admin\Resources\CandidateResource\Pages\ViewCandidate::route('/{record}'),
        ];
    }
}
