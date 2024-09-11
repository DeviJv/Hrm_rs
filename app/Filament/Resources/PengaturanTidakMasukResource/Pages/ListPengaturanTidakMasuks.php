<?php

namespace App\Filament\Resources\PengaturanTidakMasukResource\Pages;

use App\Filament\Resources\PengaturanTidakMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanTidakMasuks extends ListRecords
{
    protected static string $resource = PengaturanTidakMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
