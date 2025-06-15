<?php

namespace App\Filament\Resources\PasienResource\Pages;

use Filament\Actions;
use Filament\Actions\ImportAction;
use App\Filament\Imports\PasienImporter;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PasienResource;

class ListPasiens extends ListRecords {
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
            ImportAction::make('import_pasien')
                ->importer(PasienImporter::class)
        ];
    }
}