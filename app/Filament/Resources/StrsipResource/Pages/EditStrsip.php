<?php

namespace App\Filament\Resources\StrsipResource\Pages;

use App\Filament\Resources\StrsipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStrsip extends EditRecord
{
    protected static string $resource = StrsipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
