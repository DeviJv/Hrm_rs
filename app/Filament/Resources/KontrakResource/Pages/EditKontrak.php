<?php

namespace App\Filament\Resources\KontrakResource\Pages;

use Filament\Actions;
use App\Models\Reminder;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KontrakResource;
use App\Models\Kontrak;

class EditKontrak extends EditRecord
{
    protected static string $resource = KontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    $delete_reminder = Reminder::where('remindable_type', Kontrak::class)->where('remindable_id', $this->record->id)->delete();
                }),
        ];
    }
}