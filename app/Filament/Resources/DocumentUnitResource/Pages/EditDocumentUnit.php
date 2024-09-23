<?php

namespace App\Filament\Resources\DocumentUnitResource\Pages;

use App\Filament\Resources\DocumentUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentUnit extends EditRecord
{
    protected static string $resource = DocumentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
