<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Pas de création manuelle d'utilisateurs (OAuth uniquement)
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous'),
            
            'google' => Tab::make('Google')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('google_id'))
                ->badge(fn () => \App\Models\User::whereNotNull('google_id')->count()),
            
            'facebook' => Tab::make('Facebook')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('facebook_id'))
                ->badge(fn () => \App\Models\User::whereNotNull('facebook_id')->count()),
            
            'verified' => Tab::make('Vérifiés')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(fn () => \App\Models\User::whereNotNull('email_verified_at')->count()),
            
            'active' => Tab::make('Actifs (ont voté)')
                ->modifyQueryUsing(fn (Builder $query) => $query->has('votes'))
                ->badge(fn () => \App\Models\User::has('votes')->count()),
        ];
    }
}