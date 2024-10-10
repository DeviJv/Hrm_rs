<?php

namespace App\Filament\Exports;

use App\Models\Strsip;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StrsipExporter extends Exporter
{
    protected static ?string $model = Strsip::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('karyawan.nama')
                ->label('Nama Karyawan'),
            ExportColumn::make('karyawan.universitas')
                ->label('Universitas'),
            ExportColumn::make('sip'),
            ExportColumn::make('str'),
            ExportColumn::make('masa_berlaku_str'),
            ExportColumn::make('masa_berlaku_sip'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your strsip export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
