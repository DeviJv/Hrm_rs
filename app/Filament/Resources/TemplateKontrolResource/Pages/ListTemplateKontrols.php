<?php

namespace App\Filament\Resources\TemplateKontrolResource\Pages;

use App\Filament\Resources\TemplateKontrolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateKontrols extends ListRecords
{
    protected static string $resource = TemplateKontrolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
