<?php

namespace App\Filament\Resources\KewajibanKaryawanResource\Pages;

use App\Filament\Resources\KewajibanKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKewajibanKaryawan extends EditRecord
{
    protected static string $resource = KewajibanKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
