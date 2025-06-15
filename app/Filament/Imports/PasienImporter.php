<?php

namespace App\Filament\Imports;

use App\Models\Pasien;
use Filament\Forms\Components\Select;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class PasienImporter extends Importer {
    protected static ?string $model = Pasien::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('bidanMitra')
                ->requiredMapping()
                ->relationship(resolveUsing: 'nama')
                ->rules(['required']),
            ImportColumn::make('tindakan')
                ->example('Masukan Nama Tindakan Pada Table Tindakan')
                ->relationship('tindakan', 'nama_tindakan'),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('operasi')
                ->example('Pilih Salah Satu Dari : ya atau tidak')
                ->rules(['max:255']),
            ImportColumn::make('no_tlp')
                ->rules(['max:255']),
            ImportColumn::make('kelas')
                ->rules(['max:255'])
                ->example('Pilih Salah Satu Dari : kelas 3,kelas 2,kelas 1,VIP,SVIP,Suite Room'),
            ImportColumn::make('pasien_rujukan')
                ->label('Tipe Kunjungan')
                ->example('Pilih Salah Satu Dari : Rawat Inap,Rawat Jalan')
                ->rules(['max:255']),
            ImportColumn::make('jenis')
                ->rules(['max:255']),
            ImportColumn::make('status')
                ->example('Pilih Salah Satu Dari : diterima/ditolak')
                ->rules(['max:255']),
            ImportColumn::make('keterangan')
                ->rules(['max:255']),
            // ImportColumn::make('jaminan')
            //     ->rules(['max:255']),
            ImportColumn::make('usia')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Pasien {
        // return Pasien::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Pasien();
    }

    public static function getCompletedNotificationBody(Import $import): string {
        $body = 'Your pasien import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}