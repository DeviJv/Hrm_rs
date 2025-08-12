<?php

namespace App\Filament\Resources\MasterFeeRujukanResource\Pages;

use App\Filament\Resources\MasterFeeRujukanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterFeeRujukan extends EditRecord
{
    protected static string $resource = MasterFeeRujukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
