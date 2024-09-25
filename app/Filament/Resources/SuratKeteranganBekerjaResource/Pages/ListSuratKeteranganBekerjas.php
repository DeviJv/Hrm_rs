<?php

namespace App\Filament\Resources\SuratKeteranganBekerjaResource\Pages;

use App\Filament\Resources\SuratKeteranganBekerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratKeteranganBekerjas extends ListRecords
{
    protected static string $resource = SuratKeteranganBekerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
