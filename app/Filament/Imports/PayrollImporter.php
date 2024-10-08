<?php

namespace App\Filament\Imports;

use App\Models\Payroll;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PayrollImporter extends Importer
{
    protected static ?string $model = Payroll::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('karyawan')
                ->label('nama karyawan')
                ->relationship(resolveUsing: 'nama'),
            ImportColumn::make('gaji_pokok'),
            ImportColumn::make('transport'),
            ImportColumn::make('makan'),
            ImportColumn::make('fungsional')
                ->label('Fungsional'),
            ImportColumn::make('fungsional_it')
                ->label('Fungsional Khusus'),
            ImportColumn::make('tunjangan')
                ->label('Jabatan'),
            ImportColumn::make('bpjs_kesehatan'),
            ImportColumn::make('bpjs_ketenagakerjaan'),
        ];
    }

    public function resolveRecord(): ?Payroll
    {

        // return Payroll::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'karyawan_id' => $this->data['nama'],
        // ]);

        return new Payroll();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your payroll import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}