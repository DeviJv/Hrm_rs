<?php

namespace App\Filament\Exports;

use App\Models\Lembur;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LemburExporter extends Exporter
{
    protected static ?string $model = Lembur::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Dibuat Oleh'),
            ExportColumn::make('karyawan.nama')
                ->label('Nama Karyawan'),
            ExportColumn::make('karyawan.department')
                ->label('Department'),

            ExportColumn::make('tgl_lembur')
                ->label('Tanggal Lembur'),
            ExportColumn::make('jm_mulai')
                ->label('Jam Mulai'),
            ExportColumn::make('jm_selesai')
                ->label('Jam Selesai'),
            ExportColumn::make('jumlah_jam')
                ->label('Jumlah Jam'),
            ExportColumn::make('harga_lembur'),
            ExportColumn::make('harga_perjam'),
            ExportColumn::make('harga_jam_pertama'),
            ExportColumn::make('harga_total_jam'),
            ExportColumn::make('total_lembur'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your lembur export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}