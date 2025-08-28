<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->email === 'jeanluc@bigfiveabidjan.com'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations utilisateur')
                    ->schema([
                        Infolists\Components\ImageEntry::make('avatar')
                            ->label('Avatar')
                            ->circular()
                            ->size(80)
                            ->defaultImageUrl('/images/placeholder-avatar.svg'),
                        
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nom'),
                        
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        
                        Infolists\Components\IconEntry::make('email_verified_at')
                            ->label('Email vérifié')
                            ->boolean(),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Inscrit le')
                            ->dateTime('d/m/Y à H:i'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Activité')
                    ->schema([
                        Infolists\Components\TextEntry::make('votes_count')
                            ->label('Nombre de votes')
                            ->getStateUsing(fn ($record) => $record->votes()->count()),
                        
                        Infolists\Components\TextEntry::make('candidates_count')
                            ->label('Nombre de candidatures')
                            ->getStateUsing(fn ($record) => $record->candidates()->count()),
                        
                        Infolists\Components\TextEntry::make('last_vote')
                            ->label('Dernier vote')
                            ->getStateUsing(fn ($record) => $record->votes()->latest()->first()?->created_at?->format('d/m/Y à H:i') ?? 'Jamais'),
                    ])
                    ->columns(3),
            ]);
    }
}