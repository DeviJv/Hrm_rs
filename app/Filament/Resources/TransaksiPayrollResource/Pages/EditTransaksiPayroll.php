<?php

namespace App\Filament\Resources\TransaksiPayrollResource\Pages;

use Filament\Actions;
use App\Models\Piutang;
use App\Models\Koperasi;
use App\Models\PembayaranPiutang;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TransaksiPayrollResource;
use App\Models\PembayaranKoperasi;

class EditTransaksiPayroll extends EditRecord
{
    protected static string $resource = TransaksiPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->databaseTransaction()
                ->requiresConfirmation()
                ->form([
                    TextInput::make('password')
                        ->required()
                        ->password()
                        ->currentPassword()
                ])
                ->before(function (Model $record) {
                    if ($record->piutang > 0) {
                        $get_piutang = Piutang::where('karyawan_id', $record->karyawan_id)
                            ->whereMonth('created_at', '=', date('m', strtotime($record->created_at)))->first();
                        $pembayaran_piutang = PembayaranPiutang::where('piutang_id', $get_piutang->id)
                            ->whereDate('created_at', '=', $record->created_at)->delete();
                        $get_piutang->status = "UNPAID";
                        $get_piutang->save();
                    }
                    if ($record->koperasi > 0) {
                        $koperasi = Koperasi::where('karyawan_id', $record->karyawan_id)
                            ->whereMonth('created_at', '=', date('m', strtotime($record->created_at)))->first();
                        $pembayaran_koperasi = PembayaranKoperasi::where('koperasi_id', $koperasi->id)
                            ->whereDate('created_at', '=', $record->created_at)->delete();
                        $koperasi->status = "UNPAID";
                        $koperasi->save();
                    }
                }),
        ];
    }
}