<?php

namespace App\Filament\Resources\TransaksiPayrollResource\Pages;

use App\Filament\Resources\TransaksiPayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPayrolls extends ListRecords
{
    protected static string $resource = TransaksiPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
