<?php

namespace App\Filament\Resources\PengaturanPajakResource\Pages;

use App\Filament\Resources\PengaturanPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanPajak extends EditRecord
{
    protected static string $resource = PengaturanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
