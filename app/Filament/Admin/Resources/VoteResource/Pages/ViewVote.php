<?php

namespace App\Filament\Admin\Resources\VoteResource\Pages;

use App\Filament\Admin\Resources\VoteResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewVote extends ViewRecord
{
    protected static string $resource = VoteResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations du vote')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('ID du vote'),
                        Infolists\Components\TextEntry::make('candidate.full_name')
                            ->label('Candidat'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Utilisateur'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('Adresse IP'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date et heure')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}