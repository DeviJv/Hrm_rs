<?php

namespace App\Filament\Resources\StrsipResource\Pages;

use Filament\Actions;
use App\Models\Strsip;
use App\Models\Reminder;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StrsipResource;

class EditStrsip extends EditRecord
{
    protected static string $resource = StrsipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    $delete_reminder = Reminder::where('remindable_type', Strsip::class)->where('remindable_id', $this->record->id)->delete();
                }),
        ];
    }
}