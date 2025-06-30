<?php

namespace App\Filament\Resources\TemplateKontrolResource\Pages;

use App\Filament\Imports\TemplateKontrolImporter;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TemplateKontrolResource;

class ListTemplateKontrols extends ListRecords {
    protected static string $resource = TemplateKontrolResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
            ImportAction::make('import_template_kontrol')
                ->importer(TemplateKontrolImporter::class)

        ];
    }
}