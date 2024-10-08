<?php

namespace App\Filament\Resources\LemburResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Lembur;
use App\Models\Karyawan;
use Filament\Actions\Action as FAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Resources\LemburResource;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class CreateLembur extends CreateRecord
{
    protected static string $resource = LemburResource::class;

    protected function getCreateFormAction(): FAction
    {
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

    protected function beforeCreate(): void
    {
        $data = $this->data;
        $session_name = auth()->user()->name;
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();
        // $check_tgl = Lembur::where('karyawan_id', $data['karyawan_id'])
        //     ->whereDate('tgl_lembur', '=', $data['tgl_lembur'])->count();
        // if ($check_tgl > 0) {
        //     Notification::make()
        //         ->danger()
        //         ->title("Ops <b>{$session_name}</b> input lembur gagal Gagal!")
        //         ->body("Mohon maaf data karyawan <b>{$karyawan->nama}</b> pada tanggal {$data['tgl_lembur']} sudah di input!<br> pilih karyawan
        //         lainnya atau ubah tanggal lembur atau klik batal untuk membatalkan.")
        //         ->send();
        //     $this->halt();
        // }
        // Runs before the form fields are saved to the database.
    }

    protected function afterCreate(): void
    {
        $session_name = auth()->user()->name;
        $data = $this->data;

        $user_notif = User::role('approval')->get();
        $auth_user = auth()->user()->name;
        $karyawan = Karyawan::where('id', $data['karyawan_id'])->first();

        // Notification::make()
        //     ->icon('heroicon-o-information-circle')
        //     ->iconColor('info')
        //     ->title("Hai Kamu Punya Info Baru Nih")
        //     ->body("Saudara/i <b>{$karyawan->nama}</b> Telah Mengajukan Lembur Pada Tanggal <b>{$data['tgl_lembur']}</b>, Dari Jam <b>{$data['jm_mulai']}</b> Sampai Jam <b>{$data['jm_selesai']}</b><br/>Dengan Total Jumlah Jam <b>" . $data['jumlah_jam'] . "</b> ")
        //     ->actions([
        //         Action::make('setujui')
        //             ->label('Ok! Setuju')
        //             ->color('success')
        //             ->markAsRead()
        //             ->url(fn() => route('lembur.approve', $this->record)),
        //         Action::make('tolak')
        //             ->label('Engga Ah Tolak Aja!')
        //             ->markAsRead()
        //             ->url(fn() => route('lembur.decline', $this->record))
        //             ->color('danger'),
        //         Action::make('detail')
        //             ->label('Eh Tunggu!')
        //             ->url(fn() => LemburResource::getUrl('edit', ['record' => $this->record]), $shouldOpenInNewTab = true)
        //             ->color('info'),

        //     ])
        //     ->sendToDatabase($user_notif);
        // foreach ($user_notif as $user) {
        //     event(new DatabaseNotificationsSent($user));
        // }
        // Notification::make()
        //     ->icon('heroicon-o-information-circle')
        //     ->iconColor('info')
        //     ->title("Hai Kamu Punya Info Baru Nih")
        //     ->body("Saudara/i <b>{$karyawan->nama}</b> Telah Mengajukan Lembur Pada Tanggal <b>{$data['tgl_lembur']}</b>, Dari Jam <b>{$data['jm_mulai']}</b> Sampai Jam <b>{$data['jm_selesai']}</b><br/>Dengan Total Jumlah Jam <b>" . $data['jumlah_jam'] . "</b> ")
        //     ->actions([
        //         Action::make('setujui')
        //             ->label('Ok! Setuju')
        //             ->color('success')
        //             ->markAsRead()
        //             ->url(fn() => route('lembur.approve', $this->record)),
        //         Action::make('tolak')
        //             ->label('Engga Ah Tolak Aja!')
        //             ->markAsRead()
        //             ->url(fn() => route('lembur.decline', $this->record))
        //             ->color('danger'),
        //         Action::make('detail')
        //             ->label('Eh Tunggu!')
        //             ->url(fn() => LemburResource::getUrl('edit', ['record' => $this->record]), $shouldOpenInNewTab = true)
        //             ->color('info'),

        //     ])
        //     ->broadcast($user_notif);
    }
}