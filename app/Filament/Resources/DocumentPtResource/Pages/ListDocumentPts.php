<?php

namespace App\Filament\Resources\DocumentPtResource\Pages;

use App\Filament\Resources\DocumentPtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentPts extends ListRecords
{
    protected static string $resource = DocumentPtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
