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
            ExportColumn::make('nik')
                ->rules(['max:255']),
            ExportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ExportColumn::make('jk')
                ->rules(['max:255']),
            ExportColumn::make('agama')
                ->rules(['max:255']),
            ExportColumn::make('nakes')
                ->rules(['max:255']),
            ExportColumn::make('department')
                ->rules(['max:255']),
            ExportColumn::make('jabatan')
                ->rules(['max:255']),
            ExportColumn::make('tgl_masuk')
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    $state = Carbon::createFromFormat('d/m/Y', $state)->format('Y-m-d');
                    return $state;
                })->rules(['date']),
            ExportColumn::make('tgl_lahir')
                ->rules(['max:255']),
            ExportColumn::make('status')
                ->rules(['max:255']),
            ExportColumn::make('nik_ktp')
                ->rules(['max:255']),
            ExportColumn::make('pendidikan')
                ->rules(['max:255']),
            ExportColumn::make('universitas')
                ->rules(['max:255']),
            ExportColumn::make('no_ijazah')
                ->rules(['max:255']),
            ExportColumn::make('str')
                ->rules(['max:255']),
            ExportColumn::make('masa_berlaku')
                ->rules(['max:255']),
            ExportColumn::make('sip')
                ->rules(['max:255']),
            ExportColumn::make('no_tlp')
                ->rules(['max:255']),
            ExportColumn::make('email')
                ->rules(['max:255']),
            ExportColumn::make('alamat'),
            // ExportColumn::make('aktif')
            //     ->requiredMapping()
            //     ->boolean()
            //     ->rules(['required', 'boolean']),
            ExportColumn::make('bank')
                ->rules(['max:255']),
            ExportColumn::make('no_rekening')
                ->rules(['max:255']),
            ExportColumn::make('nip')
                ->rules(['max:255']),
            ExportColumn::make('no_sk')
                ->rules(['max:255']),
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