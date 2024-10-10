<?php

namespace App\Filament\Resources\StrsipResource\Pages;

use App\Filament\Resources\StrsipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStrsips extends ListRecords
{
    protected static string $resource = StrsipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
