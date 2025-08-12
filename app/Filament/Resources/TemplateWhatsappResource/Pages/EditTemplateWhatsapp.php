<?php

namespace App\Filament\Resources\TemplateWhatsappResource\Pages;

use App\Filament\Resources\TemplateWhatsappResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateWhatsapp extends EditRecord
{
    protected static string $resource = TemplateWhatsappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
