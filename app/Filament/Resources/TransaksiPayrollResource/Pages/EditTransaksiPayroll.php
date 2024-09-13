<?php

namespace App\Filament\Resources\TransaksiPayrollResource\Pages;

use App\Filament\Resources\TransaksiPayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPayroll extends EditRecord
{
    protected static string $resource = TransaksiPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
