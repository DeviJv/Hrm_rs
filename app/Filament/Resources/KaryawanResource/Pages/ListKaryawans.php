<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Imports\KaryawanImporter;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\KaryawanResource;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(KaryawanImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}