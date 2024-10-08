<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Exports\KaryawanExporter;
use App\Filament\Imports\KaryawanImporter;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\KaryawanResource;
use Filament\Actions\ExportAction;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(KaryawanImporter::class),
            ExportAction::make()
                ->exporter(KaryawanExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}