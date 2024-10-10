<?php

namespace App\Filament\Exports;

use Carbon\Carbon;
use App\Models\Karyawan;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class KaryawanExporter extends Exporter
{
    protected static ?string $model = Karyawan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nik'),
            ExportColumn::make('nama'),
            ExportColumn::make('jk'),
            ExportColumn::make('agama'),
            ExportColumn::make('nakes'),
            ExportColumn::make('department'),
            ExportColumn::make('jabatan'),
            ExportColumn::make('tgl_masuk'),
            ExportColumn::make('tgl_lahir'),
            ExportColumn::make('status'),
            ExportColumn::make('nik_ktp'),
            ExportColumn::make('pendidikan'),
            ExportColumn::make('universitas'),
            ExportColumn::make('no_ijazah'),
            // ExportColumn::make('str'),
            // ExportColumn::make('masa_berlaku'),
            // ExportColumn::make('sip'),
            ExportColumn::make('no_tlp'),
            ExportColumn::make('email'),
            ExportColumn::make('alamat'),
            // ExportColumn::make('aktif')
            //     ->requiredMapping()
            //     ->boolean()
            //     ->rules(['required', 'boolean']),
            ExportColumn::make('bank'),
            ExportColumn::make('no_rekening'),
            ExportColumn::make('nip'),
            ExportColumn::make('no_sk'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your karyawan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}