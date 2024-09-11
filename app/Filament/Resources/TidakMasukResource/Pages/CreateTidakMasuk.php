<?php

namespace App\Filament\Resources\TidakMasukResource\Pages;

use Filament\Actions;
use App\Models\Tidak_masuk;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TidakMasukResource;
use App\Models\PengaturanTidakMasuk;

class CreateTidakMasuk extends CreateRecord
{
    protected static string $resource = TidakMasukResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->data;
        $session_name = auth()->user()->name;
        $get_kuota = PengaturanTidakMasuk::where('nama', $data['keterangan'])->first();
        $check_tgl = Tidak_masuk::where('karyawan_id', $data['karyawan_id'])
            ->whereDate('tgl_mulai', '=', $data['tgl_mulai'])->count();
        if ($check_tgl > 0) {
            Notification::make()
                ->danger()
                ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
                ->body("Mohon maaf <b>{$session_name}</b> tanggal mulai yang kamu Masukan Sudah Ada!.")

                ->send();
            $this->halt();
        }
        $check_kuota = Tidak_masuk::where('karyawan_id', $data['karyawan_id'])->where('keterangan', $data['keterangan'])
            ->whereMonth('tgl_mulai', '=', date('m', strtotime($data['tgl_mulai'])))->count();
        if ($data['keterangan'] == "cuti") {
            if ($check_kuota >= $get_kuota->maximal) {
                Notification::make()
                    ->danger()
                    ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
                    ->body("Mohon Maaf <b>{$session_name}</b> Kuota Kamu Untuk <b>{$data['keterangan']}</b> Bulan <strong>" . date('F', strtotime($data['tgl_mulai'])) . "</strong> Sudah Penuh!.")
                    ->send();
                $this->halt();
            }
        }

        // Runs before the form fields are saved to the database.
    }
}
