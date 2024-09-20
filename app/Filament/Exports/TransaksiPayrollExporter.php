<?php

namespace App\Filament\Exports;

use App\Models\TransaksiPayroll;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransaksiPayrollExporter extends Exporter
{
    protected static ?string $model = TransaksiPayroll::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Dibuat Oleh'),
            ExportColumn::make('karyawan.nama')
                ->label('Nama Karyawan'),
            ExportColumn::make('tunjangan'),
            ExportColumn::make('gaji_pokok'),
            ExportColumn::make('makan'),
            ExportColumn::make('insentif'),
            ExportColumn::make('bpjs_kesehatan'),
            ExportColumn::make('ketenagakerjaan'),
            ExportColumn::make('pajak'),
            ExportColumn::make('tidak_masuk')
                ->label('Izin'),
            ExportColumn::make('piutang'),
            ExportColumn::make('lembur'),
            ExportColumn::make('total'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('transport'),
            ExportColumn::make('koperasi'),
            ExportColumn::make('jabatan'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('penyesuaian'),
            ExportColumn::make('fungsional')
                ->label('Fungsional Umum'),
            ExportColumn::make('fungsional_it')
                ->label('Fungsional Khusus'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaksi payroll export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
