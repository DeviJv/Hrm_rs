<?php

namespace App\Filament\Resources\PengaturanTidakMasukResource\Pages;

use App\Filament\Resources\PengaturanTidakMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanTidakMasuk extends EditRecord
{
    protected static string $resource = PengaturanTidakMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
