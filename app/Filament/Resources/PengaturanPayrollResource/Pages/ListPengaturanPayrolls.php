<?php

namespace App\Filament\Resources\PengaturanPayrollResource\Pages;

use App\Filament\Resources\PengaturanPayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanPayrolls extends ListRecords
{
    protected static string $resource = PengaturanPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
