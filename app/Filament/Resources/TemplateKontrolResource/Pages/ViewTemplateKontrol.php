<?php

namespace App\Filament\Resources\TemplateKontrolResource\Pages;

use App\Filament\Resources\TemplateKontrolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTemplateKontrol extends ViewRecord
{
    protected static string $resource = TemplateKontrolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
