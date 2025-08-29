<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
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
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
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
                        if ($record->status === 'pending') {
                            $approveUrl = route('admin.candidates.approve', $record);
                            $rejectUrl = route('admin.candidates.reject', $record);
                            return "<div class='flex space-x-2'>
                                <a href='{$approveUrl}' class='bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm'>
                                    ✓ Approuver
                                </a>
                                <a href='{$rejectUrl}' class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm' 
                                   onclick='return confirm(\"Êtes-vous sûr de vouloir rejeter ce candidat ?\")'>
                                    ✗ Rejeter
                                </a>
                            </div>";
                        }
                        
                        return match ($record->status) {
                            'approved' => '<span class="text-green-600 font-medium">✓ Approuvé</span>',
                            'rejected' => '<span class="text-red-600 font-medium">✗ Rejeté</span>',
                            default => '-'
                        };
                    })
                    ->html(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'create' => \App\Filament\Admin\Resources\CandidateResource\Pages\CreateCandidate::route('/create'),
            'edit' => \App\Filament\Admin\Resources\CandidateResource\Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}