<?php

namespace App\Filament\Resources\PengaturanTidakMasukResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PengaturanTidakMasukResource;

class CreatePengaturanTidakMasuk extends CreateRecord
{
    protected static string $resource = PengaturanTidakMasukResource::class;


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