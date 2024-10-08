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
            ImportColumn::make('karyawan.nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('gaji_pokok')
                ->rules(['required', 'max:255']),
            ImportColumn::make('transport')
                ->rules(['required', 'max:255']),
            ImportColumn::make('makan')
                ->rules(['required', 'max:255']),
            ImportColumn::make('fungsional')
                ->rules(['required', 'max:255']),
            ImportColumn::make('fungsional_it')
                ->rules(['required', 'max:255']),
            ImportColumn::make('tunjangan')
                ->rules(['required', 'max:255']),
            ImportColumn::make('bpjs_kesehatan')
                ->rules(['required', 'max:255']),
            ImportColumn::make('bpjs_ketenagakerjaan')
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Payroll
    {
        // return Payroll::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'karyawan_id' => $this->data['karyawan.nama'],
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