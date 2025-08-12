<?php

namespace App\Filament\Resources\TemplateWhatsappResource\Pages;

use App\Filament\Resources\TemplateWhatsappResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTemplateWhatsapp extends ViewRecord
{
    protected static string $resource = TemplateWhatsappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
