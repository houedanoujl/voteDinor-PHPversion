<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class EnvironmentSettings extends Page
{
    protected static ?string $navigationLabel = 'Variables Environnement';

    protected static ?string $title = 'Configuration Environment';

    protected static ?int $navigationSort = 10;

    public function getView(): string
    {
        return 'filament.admin.pages.environment-settings';
    }

    public function getEnvironmentVariables(): array
    {
        return [
            'Configuration générale' => [
                'APP_NAME' => env('APP_NAME', 'Laravel'),
                'APP_URL' => env('APP_URL', 'http://localhost'),
                'APP_DEBUG' => env('APP_DEBUG', false) ? 'true' : 'false',
                'APP_ENV' => env('APP_ENV', 'local'),
            ],
            'Base de données' => [
                'DB_HOST' => env('DB_HOST', 'localhost'),
                'DB_DATABASE' => env('DB_DATABASE', ''),
                'DB_USERNAME' => env('DB_USERNAME', ''),
                'DB_PASSWORD' => str_repeat('*', strlen(env('DB_PASSWORD', ''))),
            ],
            'Réseaux sociaux' => [
                'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID', 'Non configuré'),
                'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET') ? str_repeat('*', 20) : 'Non configuré',
                'FACEBOOK_CLIENT_ID' => env('FACEBOOK_CLIENT_ID', 'Non configuré'),
                'FACEBOOK_CLIENT_SECRET' => env('FACEBOOK_CLIENT_SECRET') ? str_repeat('*', 20) : 'Non configuré',
            ],
            'Google Analytics' => [
                'GOOGLE_ANALYTICS_TRACKING_ID' => env('GOOGLE_ANALYTICS_TRACKING_ID', 'Non configuré'),
            ],
            'WhatsApp' => [
                'WHATSAPP_API_URL' => env('WHATSAPP_API_URL', 'Non configuré'),
                'WHATSAPP_API_TOKEN' => env('WHATSAPP_API_TOKEN') ? str_repeat('*', 20) : 'Non configuré',
            ],
            'Email' => [
                'MAIL_HOST' => env('MAIL_HOST', 'Non configuré'),
                'MAIL_PORT' => env('MAIL_PORT', '587'),
                'MAIL_USERNAME' => env('MAIL_USERNAME', 'Non configuré'),
                'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'Non configuré'),
            ],
        ];
    }
}