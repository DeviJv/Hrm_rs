<?php

namespace App\Filament\Exports;

use App\Models\TransaksiPayroll;
use Filament\Actions\Exports\Exporter;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;

class TransaksiPayrollExporter extends Exporter
{
    protected static ?string $model = TransaksiPayroll::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name')
                ->label('Dibuat Oleh'),
            ExportColumn::make('karyawan.nama')
                ->label('Nama Karyawan'),
            ExportColumn::make('karyawan.no_sk')
                ->label('NPWP'),
            ExportColumn::make('karyawan.jabatan'),
            ExportColumn::make('gaji_pokok')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),
            ExportColumn::make('transport')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),
            ExportColumn::make('makan')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('sub_total_1')
                ->state(function (TransaksiPayroll $record) {
                    return number_format($record->gaji_pokok + $record->transport + $record->makan, 0, ',', '.');
                }),
            ExportColumn::make('penyesuaian')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),
            ExportColumn::make('insentif')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('fungsional')
                ->label('Fungsional Umum')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('fungsional_it')
                ->label('Fungsional Khusus')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('jabatan')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),
            ExportColumn::make('sub_total_2')
                ->state(function (TransaksiPayroll $record) {
                    return  number_format($record->gaji_pokok + $record->transport + $record->makan + $record->penyesuaian + $record->insentif + $record->fungsional + $record->fungsional_it + $record->jabatan, 0, ',', '.');
                }),
            ExportColumn::make('ketenagakerjaan')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('bpjs_kesehatan')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('pajak')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('koperasi')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('piutang')
                ->label('obat/catering/dll')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('tidak_masuk')
                ->label('Izin')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.')),

            ExportColumn::make('sub_total_3')
                ->state(function (TransaksiPayroll $record) {
                    return number_format($record->ketenagakerjaan + $record->bpjs_kesehatan + $record->pajak + $record->koperasi + $record->piutang, 0, ',', '.');
                }),
            ExportColumn::make('total')
                ->formatStateUsing(fn(string $state): int => number_format($state, 0, ',', '.'))
                ->label('NET INCOME'),
            // ExportColumn::make('karyawan.nik_ktp')
            //     ->label('Nomor Induk Kependudukan'),
            // ExportColumn::make('karyawan.alamat')
            //     ->label('Alamat Karyawan'),
            ExportColumn::make('payment_method'),

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

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(10)
            ->setFontName('inter')
            ->setFontColor(Color::rgb(0, 0, 0))
            ->setBackgroundColor(Color::rgb(27, 179, 32))
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
}
