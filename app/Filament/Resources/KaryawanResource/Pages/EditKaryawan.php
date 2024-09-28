<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KaryawanResource;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->form([
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->rules(['current_password'])
                ])
                ->keyBindings(['mod+s']),
        ];
    }
    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->action(fn() => $this->save())
            ->requiresConfirmation()
            ->form([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rules(['current_password'])
            ])
            ->keyBindings(['mod+s']);
    }
    protected function afterSave(): void
    {
        $user = User::where('karyawan_id', $this->record->id);
        if ($user->count() > 0) {
            $user = $user->first();
            $user->aktif = $this->record->aktif;
            $user->save();
        }
    }
}