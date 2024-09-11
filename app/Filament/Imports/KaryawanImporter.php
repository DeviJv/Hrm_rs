<?php

namespace App\Filament\Imports;

use App\Models\Karyawan;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class KaryawanImporter extends Importer
{
    protected static ?string $model = Karyawan::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nik')
                ->rules(['max:255']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('jk')
                ->rules(['max:255']),
            ImportColumn::make('agama')
                ->rules(['max:255']),
            ImportColumn::make('nakes')
                ->rules(['max:255']),
            ImportColumn::make('department')
                ->rules(['max:255']),
            ImportColumn::make('jabatan')
                ->rules(['max:255']),
            ImportColumn::make('tgl_masuk')
                // ->castStateUsing(function ($state) {
                //     if (blank($state)) {
                //         return null;
                //     }
                //     $state = date('Y-m-d', strtotime($state));
                //     return $state;
                // })
                ->rules(['date']),
            ImportColumn::make('tgl_lahir')
                ->rules(['max:255']),
            ImportColumn::make('status')
                ->rules(['max:255']),
            ImportColumn::make('nik_ktp')
                ->rules(['max:255']),
            ImportColumn::make('pendidikan')
                ->rules(['max:255']),
            ImportColumn::make('universitas')
                ->rules(['max:255']),
            ImportColumn::make('no_ijazah')
                ->rules(['max:255']),
            ImportColumn::make('str')
                ->rules(['max:255']),
            ImportColumn::make('masa_berlaku')
                ->rules(['max:255']),
            ImportColumn::make('sip')
                ->rules(['max:255']),
            ImportColumn::make('no_tlp')
                ->rules(['max:255']),
            ImportColumn::make('email')
                ->rules(['max:255']),
            ImportColumn::make('alamat'),
            // ImportColumn::make('aktif')
            //     ->requiredMapping()
            //     ->boolean()
            //     ->rules(['required', 'boolean']),
            ImportColumn::make('bank')
                ->rules(['max:255']),
            ImportColumn::make('no_rekening')
                ->rules(['max:255']),
            ImportColumn::make('nip')
                ->rules(['max:255']),
            ImportColumn::make('no_sk')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Karyawan
    {
        return Karyawan::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'nama' => $this->data['nama'],
        ]);

        return new Karyawan();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your karyawan import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}