<?php

namespace App\Filament\Resources\KoperasiResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\KoperasiResource;

class CreateKoperasi extends CreateRecord
{
    protected static string $resource = KoperasiResource::class;

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