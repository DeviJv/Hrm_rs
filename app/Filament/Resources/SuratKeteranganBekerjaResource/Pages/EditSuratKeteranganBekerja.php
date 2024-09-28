<?php

namespace App\Filament\Resources\SuratKeteranganBekerjaResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SuratKeteranganBekerjaResource;

class EditSuratKeteranganBekerja extends EditRecord
{
    protected static string $resource = SuratKeteranganBekerjaResource::class;

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
}