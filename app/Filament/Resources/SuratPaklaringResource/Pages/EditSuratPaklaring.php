<?php

namespace App\Filament\Resources\SuratPaklaringResource\Pages;

use App\Filament\Resources\SuratPaklaringResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratPaklaring extends EditRecord
{
    protected static string $resource = SuratPaklaringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
