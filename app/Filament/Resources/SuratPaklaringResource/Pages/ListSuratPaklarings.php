<?php

namespace App\Filament\Resources\SuratPaklaringResource\Pages;

use App\Filament\Resources\SuratPaklaringResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPaklarings extends ListRecords
{
    protected static string $resource = SuratPaklaringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
