<?php

namespace App\Filament\Resources\TidakMasukResource\Pages;

use App\Filament\Resources\TidakMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTidakMasuks extends ListRecords
{
    protected static string $resource = TidakMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
