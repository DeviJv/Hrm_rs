<?php

namespace App\Filament\Resources\TidakMasukResource\Pages;

use Filament\Actions;
use App\Models\Karyawan;
use App\Models\Tidak_masuk;
use Filament\Actions\Action;
use App\Models\PengaturanTidakMasuk;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TidakMasukResource;

class EditTidakMasuk extends EditRecord
{
    protected static string $resource = TidakMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->form([
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->rules(['current_password'])
                ])
                ->keyBindings(['mod+s']),
        ];
    }
    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->action(fn() => $this->save())
            ->requiresConfirmation()
            ->form([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rules(['current_password'])
            ])
            ->keyBindings(['mod+s']);
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $dari = date_create($data['tgl_mulai']);
        $sampai = date_create($data['tgl_akhir']);
        $hitung = date_diff($dari, $sampai);
        $data['jumlah_hari'] = $hitung->d;
        return $data;
    }


    protected function beforeSave(): void
    {
        $data = $this->data;
        $dari = date_create($data['tgl_mulai']);
        $sampai = date_create($data['tgl_akhir']);
        $hitung = date_diff($dari, $sampai);
        $session_name = auth()->user()->name;
        $get_kuota = PengaturanTidakMasuk::where('nama', $data['keterangan'])->first();
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
        $check_tgl = Tidak_masuk::where('karyawan_id', $data['karyawan_id'])
            ->whereDate('tgl_mulai', '=', $data['tgl_mulai'])->count();
        if ($data['tgl_mulai'] != date('Y-m-d', strtotime($this->record->tgl_mulai)) && $data['tgl_akhir'] != date('Y-m-d', strtotime($this->record->tgl_akhir))) {

            if ($check_tgl > 0) {
                Notification::make()
                    ->danger()
                    ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
                    ->body("Mohon maaf <b>{$session_name}</b> tanggal mulai yang kamu Masukan Sudah Ada!.")
                    ->send();
                $this->halt();
            }
        }
        $check_kuota = Tidak_masuk::where('karyawan_id', $data['karyawan_id'])->where('keterangan', 'cuti')
            ->whereMonth('tgl_mulai', '=', date('m', strtotime($data['tgl_mulai'])))->sum('jumlah_hari');
        if ($data['keterangan'] == "cuti") {

            if ($data['tgl_mulai'] == date('Y-m-d', strtotime($this->record->tgl_mulai)) && $data['tgl_akhir'] == date('Y-m-d', strtotime($this->record->tgl_akhir))) {
            } else {
                $check_kuota = $check_kuota - $this->record->jumlah_hari;
                if (($hitung->d + $check_kuota) > $get_kuota->maximal) {
                    Notification::make()
                        ->danger()
                        ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
                        ->body("Mohon Maaf <b>{$session_name}</b> Kuota <b>{$karyawan->nama}</b> Untuk <b>{$data['keterangan']}</b> Bulan <strong>" . date('F', strtotime($data['tgl_mulai'])) . "</strong> Sudah Penuh!.")
                        ->send();
                    $this->halt();
                }
                if ((int)$check_kuota >= $get_kuota->maximal) {
                    Notification::make()
                        ->danger()
                        ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
                        ->body("Mohon Maaf <b>{$session_name}</b> Kuota <b>{$karyawan->nama}</b> Untuk <b>{$data['keterangan']}</b> Bulan <strong>" . date('F', strtotime($data['tgl_mulai'])) . "</strong> Sudah Penuh!.")
                        ->send();
                    $this->halt();
                }
            }
            // Runs before the form fields are saved to the database.
        }
    }
}