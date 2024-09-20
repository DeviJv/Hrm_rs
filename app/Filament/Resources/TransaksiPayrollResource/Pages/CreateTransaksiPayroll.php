<?php

namespace App\Filament\Resources\TransaksiPayrollResource\Pages;

use Filament\Actions;
use App\Models\Piutang;
use App\Models\Karyawan;
use App\Models\TransaksiPayroll;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransaksiPayrollResource;
use App\Models\Koperasi;
use App\Models\PembayaranKoperasi;
use App\Models\PembayaranPiutang;

class CreateTransaksiPayroll extends CreateRecord
{
    protected static string $resource = TransaksiPayrollResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['user_id'] = auth()->user()->id;
        return $data;
    }
    protected function beforeCreate(): void
    {
        $data = $this->data;
        $session_name = auth()->user()->name;
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
        $check = TransaksiPayroll::where('karyawan_id', $data['karyawan_id'])
            ->whereMonth('created_at', '=', date('m', strtotime($data['created_at'])))->count();
        if ($check > 0) {
            Notification::make()
                ->danger()
                ->title("Ops Input Transaksi Payroll Gagal!")
                ->body("Maaf <b>{$session_name}</b> Data Payroll <b>{$karyawan->nama}</b> Pada Bulan " . date('m', strtotime($data['created_at'])) . " Sudah Di Input!.")
                ->send();
            $this->halt();
        }
    }
    protected function afterCreate(): void
    {
        $data = $this->data;
        if ($data['piutang'] > 0) {
            $get_piutang = Piutang::where('karyawan_id', $data['karyawan_id'])
                ->whereMonth('created_at', '=', date('m', strtotime($data['created_at'])))->first();
            $bayar_piutang = PembayaranPiutang::create([
                "piutang_id" => $get_piutang->id,
                "nominal" => $data['piutang'],
                "created_at" => $data['created_at']
            ]);
            if ($data['piutang'] == $get_piutang->sub_total) {
                $get_piutang->status = "PAID";
                $get_piutang->save();
            }
        }

        if ($data['koperasi'] > 0) {
            $koperasi = Koperasi::where('karyawan_id', $data['karyawan_id'])
                ->whereMonth('created_at', '=', date('m', strtotime($data['created_at'])))->first();
            $bayar_piutang = PembayaranKoperasi::create([
                "koperasi_id" => $koperasi->id,
                "nominal" => $data['koperasi'],
                "created_at" => $data['created_at']
            ]);
            $sum_pembayaran_koperasi = PembayaranKoperasi::where('koperasi_id', $koperasi->id)->sum('nominal');
            if ($koperasi->tagihan <= $sum_pembayaran_koperasi) {
                $koperasi->status = "PAID";
                $koperasi->save();
            }
        }
    }
}
