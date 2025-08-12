<?php

namespace App\Filament\Resources\TemplateKontrolResource\Pages;

use App\Filament\Resources\TemplateKontrolResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplateKontrol extends CreateRecord {
    protected static string $resource = TemplateKontrolResource::class;
    protected static ?string $title = "Buat Pasien Kontrol";
}
