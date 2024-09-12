<?php

namespace App\Filament\Resources\PengaturanPayrollResource\Pages;

use App\Filament\Resources\PengaturanPayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanPayroll extends EditRecord
{
    protected static string $resource = PengaturanPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
