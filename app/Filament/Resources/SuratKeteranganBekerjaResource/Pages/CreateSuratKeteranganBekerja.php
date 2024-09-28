<?php

namespace App\Filament\Resources\SuratKeteranganBekerjaResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SuratKeteranganBekerjaResource;

class CreateSuratKeteranganBekerja extends CreateRecord
{
    protected static string $resource = SuratKeteranganBekerjaResource::class;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->action(fn() => $this->create())
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