<?php

namespace App\Filament\Resources\MarkerIconResource\Pages;

use App\Filament\Resources\MarkerIconResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkerIcons extends ListRecords
{
    protected static string $resource = MarkerIconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
