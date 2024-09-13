<?php

namespace App\Filament\Resources\KoperasiResource\Pages;

use App\Filament\Resources\KoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKoperasis extends ListRecords
{
    protected static string $resource = KoperasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
