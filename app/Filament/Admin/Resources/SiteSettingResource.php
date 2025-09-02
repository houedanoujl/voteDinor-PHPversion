<?php

namespace App\Filament\Admin\Resources;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use BackedEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $pluralModelLabel = 'Paramètres du site';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Toggle::make('applications_open')
                    ->label('Candidatures ouvertes')
                    ->helperText('Activez pour permettre les nouvelles inscriptions candidats')
                    ->default(true),
                Forms\Components\Toggle::make('uploads_enabled')
                    ->label('Upload de photos activé')
                    ->helperText('Désactivez en phase de vote pour bloquer les nouveaux uploads')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('applications_open')
                    ->boolean()
                    ->label('Candidatures ouvertes'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Mise à jour'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\SiteSettingResource\Pages\ManageSiteSettings::route('/'),
        ];
    }
}


