<?php

namespace App\Filament\Resources\MasterFeeRujukanResource\Pages;

use App\Filament\Resources\MasterFeeRujukanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMasterFeeRujukan extends ViewRecord
{
    protected static string $resource = MasterFeeRujukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
