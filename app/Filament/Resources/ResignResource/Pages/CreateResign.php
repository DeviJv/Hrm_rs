<?php

namespace App\Filament\Resources\ResignResource\Pages;

use Filament\Actions;
use App\Models\Karyawan;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ResignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResign extends CreateRecord
{
    protected static string $resource = ResignResource::class;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->action(fn() => $this->create())
            ->requiresConfirmation()
            ->databaseTransaction()
            ->form([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rules(['current_password'])
            ])
            ->after(function () {
                if ($this->record != null) {
                    $karyawan = Karyawan::where('id', $this->record->karyawan_id)->update(['aktif' => false]);
                }
            })
            ->keyBindings(['mod+s']);
    }
    protected function afterCreate(): void
    {
        $data = $this->data;

        if ($data != null) {
            $karyawan = Karyawan::where('id', $data['karyawan_id'])->update(['aktif' => false]);
        }
    }
}