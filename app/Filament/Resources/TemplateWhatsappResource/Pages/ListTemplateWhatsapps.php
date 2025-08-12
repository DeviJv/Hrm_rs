<?php

namespace App\Filament\Resources\TemplateWhatsappResource\Pages;

use App\Filament\Resources\TemplateWhatsappResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateWhatsapps extends ListRecords
{
    protected static string $resource = TemplateWhatsappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
