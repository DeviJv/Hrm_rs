<?php

namespace App\Filament\Resources\PengaturanPajakResource\Pages;

use App\Filament\Imports\PengaturanPajakImporter;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PengaturanPajakResource;

class ListPengaturanPajaks extends ListRecords
{
    protected static string $resource = PengaturanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(PengaturanPajakImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
