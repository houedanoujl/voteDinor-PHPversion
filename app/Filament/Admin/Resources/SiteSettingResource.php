<?php

namespace App\Filament\Admin\Resources;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
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
                Forms\Components\Toggle::make('votes_enabled')
                    ->label('Votes activés')
                    ->helperText("Désactivez pour fermer temporairement les votes sur le site")
                    ->default(true),
                Forms\Components\TextInput::make('live_url')
                    ->label('Lien Live Facebook')
                    ->placeholder('https://www.facebook.com/...')
                    ->helperText('Saisissez le lien du live Facebook. S’il est renseigné, un bouton “En direct” apparaîtra sur le site.')
                    ->url()
                    ->maxLength(255)
                    ->default('https://www.facebook.com/share/v/172P5EMjL2/'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('applications_open')
                    ->boolean()
                    ->label('Candidatures ouvertes'),
                Tables\Columns\TextColumn::make('live_url')
                    ->label('Lien Live')
                    ->wrap()
                    ->copyable()
                    ->url(fn ($record) => $record->live_url, true)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Mise à jour'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\SiteSettingResource\Pages\ManageSiteSettings::route('/'),
        ];
    }
}



