<?php

namespace App\Filament\Resources\PengaturanPajakResource\Pages;

use App\Filament\Resources\PengaturanPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanPajaks extends ListRecords
{
    protected static string $resource = PengaturanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
