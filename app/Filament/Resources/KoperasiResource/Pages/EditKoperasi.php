<?php

namespace App\Filament\Resources\KoperasiResource\Pages;

use App\Filament\Resources\KoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKoperasi extends EditRecord
{
    protected static string $resource = KoperasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
