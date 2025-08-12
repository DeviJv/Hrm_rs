<?php

namespace App\Filament\Resources\TemplateKontrolResource\Pages;

use App\Filament\Resources\TemplateKontrolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateKontrol extends EditRecord {
    protected static string $resource = TemplateKontrolResource::class;
    protected static ?string $title = "Ubah Pasien Kontrol";

    protected function getHeaderActions(): array {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
