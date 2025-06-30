<?php

namespace App\Filament\Imports;

use App\Models\TemplateKontrol;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TemplateKontrolImporter extends Importer {
    protected static ?string $model = TemplateKontrol::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('created_at')
                ->label('Tanggal')
                ->rules(['required']),
            ImportColumn::make('no_rm')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('status')
                ->example('Pilih Salah Satu Dari Lama Atau Baru')
                ->rules(['max:255']),
            ImportColumn::make('jk')
                ->example('Pilih Salah Satu Dari pria Atau wanita')
                ->rules(['max:255']),
            ImportColumn::make('umur')
                ->numeric()
                ->rules(['max:255']),
            ImportColumn::make('alamat'),
            ImportColumn::make('no_hp')
                ->rules(['max:255']),
            ImportColumn::make('diagnosa')
                ->rules(['max:255']),
            ImportColumn::make('tindakan')
                ->rules(['max:255']),
            ImportColumn::make('hpl'),
            ImportColumn::make('tgl_kontrol'),
            ImportColumn::make('penjamin')
                ->rules(['max:255']),
            ImportColumn::make('keterangan')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?TemplateKontrol {
        // return TemplateKontrol::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new TemplateKontrol();
    }

    public static function getCompletedNotificationBody(Import $import): string {
        $body = 'Your template kontrol import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}