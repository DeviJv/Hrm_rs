<?php

namespace App\Filament\Resources\LemburResource\Pages;

use Filament\Actions;
use App\Models\Lembur;
use Filament\Notifications\Notification;
use App\Filament\Resources\LemburResource;
use App\Models\Karyawan;
use Filament\Resources\Pages\CreateRecord;

class CreateLembur extends CreateRecord
{
    protected static string $resource = LemburResource::class;


    protected function beforeCreate(): void
    {
        $data = $this->data;
        $session_name = auth()->user()->name;
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
        $check_tgl = Lembur::where('karyawan_id', $data['karyawan_id'])
            ->whereDate('tgl_lembur', '=', $data['tgl_lembur'])->count();
        if ($check_tgl > 0) {
            Notification::make()
                ->danger()
                ->title("Ops <b>{$session_name}</b> input lembur gagal Gagal!")
                ->body("Mohon maaf data karyawan <b>{$karyawan->nama}</b> pada tanggal {$data['tgl_lembur']} sudah di input!<br> pilih karyawan
                lainnya atau ubah tanggal lembur atau klik batal untuk membatalkan.")
                ->send();
            $this->halt();
        }


        // Runs before the form fields are saved to the database.
    }
}
