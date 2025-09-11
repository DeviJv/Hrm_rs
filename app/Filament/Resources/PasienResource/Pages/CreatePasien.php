<?php

namespace App\Filament\Resources\PasienResource\Pages;

use App\Filament\Resources\PasienResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePasien extends CreateRecord {
    protected static string $resource = PasienResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array {
        $test = explode('|', $data['fee'])[1] ?? null;
        $data['fee'] = $test;
        return $data;
    }
}