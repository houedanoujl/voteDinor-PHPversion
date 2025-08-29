<?php

namespace App\Filament\Admin\Resources\CandidateResource\Pages;

use App\Filament\Admin\Resources\CandidateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCandidates extends ListRecords
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction removed - form not available in this Filament version
        ];
    }
    
    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'whatsappSendRoute' => route('admin.whatsapp.send'),
            'csrfToken' => csrf_token(),
        ]);
    }
}