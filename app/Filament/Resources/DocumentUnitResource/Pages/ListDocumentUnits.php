<?php

namespace App\Filament\Resources\DocumentUnitResource\Pages;

use App\Filament\Resources\DocumentUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentUnits extends ListRecords
{
    protected static string $resource = DocumentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
