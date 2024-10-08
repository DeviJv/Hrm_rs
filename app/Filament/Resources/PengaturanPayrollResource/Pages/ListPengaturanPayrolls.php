<?php

namespace App\Filament\Resources\PengaturanPayrollResource\Pages;

use App\Filament\Imports\PayrollImporter;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PengaturanPayrollResource;

class ListPengaturanPayrolls extends ListRecords
{
    protected static string $resource = PengaturanPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(PayrollImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}