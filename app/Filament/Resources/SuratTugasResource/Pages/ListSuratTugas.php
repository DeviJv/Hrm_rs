<?php

namespace App\Filament\Resources\SuratTugasResource\Pages;

use App\Filament\Resources\SuratTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratTugas extends ListRecords
{
    protected static string $resource = SuratTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
