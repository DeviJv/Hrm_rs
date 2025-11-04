<?php

namespace App\Filament\Resources\TidakMasukResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Karyawan;
use App\Models\Tidak_masuk;
use App\Models\PengaturanTidakMasuk;
use Filament\Actions\Action as FAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TidakMasukResource;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class CreateTidakMasuk extends CreateRecord {
    protected static string $resource = TidakMasukResource::class;

    protected function getCreateFormAction(): FAction {
        return FAction::make('create')
            ->action(fn() => $this->create())
            ->requiresConfirmation()
            ->form([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rules(['current_password'])
            ])
            ->keyBindings(['mod+s']);
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        $dari = date_create($data['tgl_mulai']);
        $sampai = date_create($data['tgl_akhir']);
        $hitung = date_diff($dari, $sampai);
        $data['jumlah_hari'] = $hitung->d;
        return $data;
    }

    protected function beforeCreate(): void {
        $data = $this->data;
        $dari = date_create($data['tgl_mulai']);
        $sampai = date_create($data['tgl_akhir']);
        $hitung = date_diff($dari, $sampai);
        $session_name = auth()->user()->name;
        $get_kuota = PengaturanTidakMasuk::where('nama', $data['keterangan'])->first();
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
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
        $check_kuota = Tidak_masuk::where('karyawan_id', $data['karyawan_id'])->where('keterangan', 'cuti')
            ->whereMonth('tgl_mulai', '=', date('m', strtotime($data['tgl_mulai'])))->sum('jumlah_hari');

        // if ($data['keterangan'] == "cuti") {
        //     if ($hitung->d > $get_kuota->maximal) {
        //         Notification::make()
        //             ->danger()
        //             ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
        //             ->body("Mohon Maaf <b>{$session_name}</b> Kuota <b>{$karyawan->nama}</b> Untuk <b>{$data['keterangan']}</b> Bulan <strong>" . date('F', strtotime($data['tgl_mulai'])) . "</strong> Sudah Penuh!.")
        //             ->send();
        //         $this->halt();
        //     }
        //     if ((int)$check_kuota >= $get_kuota->maximal) {
        //         Notification::make()
        //             ->danger()
        //             ->title("Pengajuan <strong>{$data['keterangan']}</strong> Gagal!")
        //             ->body("Mohon Maaf <b>{$session_name}</b> Kuota <b>{$karyawan->nama}</b> Untuk <b>{$data['keterangan']}</b> Bulan <strong>" . date('F', strtotime($data['tgl_mulai'])) . "</strong> Sudah Penuh!.")
        //             ->send();
        //         $this->halt();
        //     }
        // }
        // Runs before the form fields are saved to the database.
    }

    protected function afterCreate(): void {
        $session_name = auth()->user()->name;
        $data = $this->data;

        $user_notif = User::role('approval')->get();
        $auth_user = auth()->user()->name;
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
        $karyawan_backup = Karyawan::where('id', $data['backup_karyawan'])->first();

        if ($data['keterangan'] == "cuti") {
            Notification::make()
                ->icon('heroicon-o-information-circle')
                ->iconColor('info')
                ->title("Hai Kamu Punya Info Baru Nih")
                ->body("Saudara/i <b>{$karyawan->nama}</b> Telah Mengajukan Cuti Untuk <b>{$data['keperluan']}</b>, Dengan Backup Karyawan <b>{$karyawan_backup->nama}</b> <br/>Pada Tanggal <b>" . date('d/m/Y', strtotime($this->record->tgl_mulai)) . "</b> Sampai Ke <b>" . date('d/m/Y', strtotime($this->record->tgl_mulai)) . "</b>.")
                ->actions([
                    Action::make('setujui')
                        ->label('Ok! Setuju')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('cuti.approve', $this->record)),
                    Action::make('tolak')
                        ->label('Engga Ah Tolak Aja!')
                        ->markAsRead()
                        ->url(fn() => route('cuti.decline', $this->record))
                        ->color('danger'),
                    Action::make('detail')
                        ->label('Eh Tunggu!')
                        ->url(fn() => TidakMasukResource::getUrl('edit', ['record' => $this->record]), $shouldOpenInNewTab = true)
                        ->color('info'),

                ])
                ->sendToDatabase($user_notif);
            foreach ($user_notif as $user) {
                event(new DatabaseNotificationsSent($user));
            }
            Notification::make()
                ->icon('heroicon-o-information-circle')
                ->iconColor('info')
                ->title("Hai Kamu Punya Info Baru Nih")
                ->body("Saudara/i <b>{$karyawan->nama}</b> Telah Mengajukan Cuti Untuk <b>{$data['keperluan']}</b>, Dengan Backup Karyawan <b>{$karyawan_backup->nama}</b> <br/>Pada Tanggal <b>" . date('d/m/Y', strtotime($this->record->tgl_mulai)) . "</b> Sampai Ke <b>" . date('d/m/Y', strtotime($this->record->tgl_mulai)) . "</b>.")
                ->actions([
                    Action::make('setujui')
                        ->label('Ok! Setuju')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('cuti.approve', $this->record)),
                    Action::make('tolak')
                        ->label('Engga Ah Tolak Aja!')
                        ->markAsRead()
                        ->url(fn() => route('cuti.decline', $this->record))
                        ->color('danger'),
                    Action::make('detail')
                        ->label('Eh Tunggu!')
                        ->url(fn() => TidakMasukResource::getUrl('edit', ['record' => $this->record]), $shouldOpenInNewTab = true)
                        ->color('info'),

                ])
                ->broadcast($user_notif);
        }
    }
}
