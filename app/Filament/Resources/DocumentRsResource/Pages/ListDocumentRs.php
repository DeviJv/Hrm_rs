<?php

namespace App\Filament\Resources\DocumentRsResource\Pages;

use App\Filament\Resources\DocumentRsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentRs extends ListRecords
{
    protected static string $resource = DocumentRsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
