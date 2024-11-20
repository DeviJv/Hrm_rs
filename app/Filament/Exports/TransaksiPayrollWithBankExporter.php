<?php

namespace App\Filament\Exports;

use App\Models\TransaksiPayroll;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransaksiPayrollWithBankExporter extends Exporter
{
    protected static ?string $model = TransaksiPayroll::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('karyawan.nama')
                ->label('Nama Karyawan'),
            ExportColumn::make('karyawan.bank')
                ->label('Bank'),
            ExportColumn::make('karyawan.no_rekening')
                ->label('No Rekening'),
            ExportColumn::make('total')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaksi payroll With Bank export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
