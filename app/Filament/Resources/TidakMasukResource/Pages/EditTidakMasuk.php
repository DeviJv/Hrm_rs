<?php

namespace App\Filament\Resources\TidakMasukResource\Pages;

use App\Filament\Resources\TidakMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTidakMasuk extends EditRecord
{
    protected static string $resource = TidakMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
