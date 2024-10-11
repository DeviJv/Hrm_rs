<?php

namespace App\Filament\Imports;

use App\Models\PengaturanPajak;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PengaturanPajakImporter extends Importer
{
    protected static ?string $model = PengaturanPajak::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('karyawan')
                ->label('nama karyawan')
                ->relationship(resolveUsing: 'nama'),
            ImportColumn::make('status'),
            ImportColumn::make('tarif'),
        ];
    }

    public function resolveRecord(): ?PengaturanPajak
    {
        // return PengaturanPajak::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new PengaturanPajak();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your pengaturan pajak import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
