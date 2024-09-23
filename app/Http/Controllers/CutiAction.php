<?php

namespace App\Http\Controllers;

use App\Models\Tidak_masuk;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\TidakMasukResource;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class CutiAction extends Controller
{

    public function approve($tidak_masuk)
    {
        $session_name = auth()->user()->name;
        $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk)->with(['karyawan', 'approved', 'decline'])->first();
        if ($tidak_masuk == null) {
            Notification::make()
                ->danger()
                ->title("Opps data cuti tidak di temukan!")
                ->send();
            return redirect()->back();
        }
        if ($tidak_masuk->status == "decline") {
            Notification::make()
                ->warning()
                ->title("Opps data pengajuan cuti <b>{$tidak_masuk->keperluan}</b> yang di ajukan oleh <b>{$tidak_masuk->karyawan->nama}</b> sudah ditolak Oleh <b>{$tidak_masuk->decline->name}</b>!")
                ->body('Apakah anda yakin ingin melakukan perubahan ini?')
                ->actions([
                    Action::make('setujui_saja')
                        ->label('Ah Setujui Sajalah')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('cuti.approve.force', $tidak_masuk)),
                    Action::make('tolak_saja')
                        ->label('Eh yaudah deh')
                        ->color('danger')
                        ->markAsRead()
                    // ->url(fn() => route('cuti.approve.force', $tidak_masuk)),
                ])
                ->send();
            return redirect()->back();
        }
        if ($tidak_masuk->status != "approved") {
            $tidak_masuk->status = "approved";
            $tidak_masuk->approved_by = auth()->user()->id;
            $tidak_masuk->updated_at = now();
            $tidak_masuk->save();
            $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! Data cuti berhasil di setujui")
                ->body("Data Cuti <b>$tidak_masuk->keperluan</b> <b>{$tidak_masuk->karyawan->nama}</b> Ini Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            Notification::make()
                ->success()
                ->title("Selamat <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda berhasil di setujui")
                ->body("Data <b>{$tidak_masuk->keperluan}</b> Anda Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($tidak_masuk->karyawan->user);

            event(new DatabaseNotificationsSent($tidak_masuk->karyawan->user));
            Notification::make()
                ->success()
                ->title("Selamat <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda berhasil di setujui")
                ->body("Data <b>{$tidak_masuk->keperluan}</b> Anda Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($tidak_masuk->karyawan->user);
            return redirect()->back();
        }
        if ($tidak_masuk->status == "approved") {
            Notification::make()
                ->danger()
                ->title("Ops <strong>{$session_name}</strong> Tidak Perlu Persetujuan Ulang!")
                ->body("Data Cuti <b>{$tidak_masuk->keperluan}</b> Ini Sudah Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            return redirect()->back();
        }
    }

    public function approve_force($tidak_masuk)
    {
        $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk)->with(['karyawan', 'approved', 'decline'])->first();

        if ($tidak_masuk->status != "approved") {
            $tidak_masuk->status = "approved";
            $tidak_masuk->approved_by = auth()->user()->id;
            $tidak_masuk->updated_at = now();

            $tidak_masuk->save();
            $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! Data cuti berhasil di setujui")
                ->body("Data <b>$tidak_masuk->keperluan</b> <b>{$tidak_masuk->karyawan->nama}</b> Ini Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            Notification::make()
                ->success()
                ->title("Selamat <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda telah di setujui")
                ->body("Data <b>$tidak_masuk->keperluan</b> Anda Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($tidak_masuk->karyawan->user);
            event(new DatabaseNotificationsSent($tidak_masuk->karyawan->user));
            Notification::make()
                ->success()
                ->title("Selamat <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda telah di setujui")
                ->body("Data <b>$tidak_masuk->keperluan</b> Anda Berhasil Di Setujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($tidak_masuk->karyawan->user);
            return redirect()->back();
        }
    }


    public function decline($tidak_masuk)
    {
        $session_name = auth()->user()->name;

        $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk)->with(['karyawan', 'approved', 'decline'])->first();
        if ($tidak_masuk == null) {
            Notification::make()
                ->danger()
                ->title("Opps data cuti tidak di temukan!")
                ->send();
            return redirect()->back();
        }
        if ($tidak_masuk->status == "approved") {
            Notification::make()
                ->warning()
                ->title("Opps data pengajuan cuti ini sudah disetujui Oleh <b>{$tidak_masuk->approved->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>!")
                ->actions([
                    Action::make('tolak_saja')
                        ->label('Ah ga boleh pokonya mah 😤')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('cuti.decline.force', $tidak_masuk)),
                    Action::make('tolak_saja')
                        ->label('Eh yaudah deh')
                        ->color('danger')
                        ->markAsRead()
                    // ->url(fn() => route('cuti.approve.force', $tidak_masuk)),
                ])
                ->send();
            return redirect()->back();
        }
        if ($tidak_masuk->status != "decline") {
            $tidak_masuk->status = "decline";
            $tidak_masuk->decline_by = auth()->user()->id;
            $tidak_masuk->updated_at = now();
            $tidak_masuk->save();
            $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! Data cuti berhasil di tolak!")
                ->body("Data Cuti <b>{$tidak_masuk->keperluan}</b> Yang Diajukan  <b>{$tidak_masuk->karyawan->nama}</b> Ini Berhasil Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda di tolak")
                ->body("Data <b>{$tidak_masuk->keperluan}</b> Anda Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($tidak_masuk->karyawan->user);
            event(new DatabaseNotificationsSent($tidak_masuk->karyawan->user));
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda di tolak")
                ->body("Data <b>{$tidak_masuk->keperluan}</b> Anda Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($tidak_masuk->karyawan->user);
            return redirect()->back();
        }
        if ($tidak_masuk->status == "decline") {
            Notification::make()
                ->danger()
                ->title("Ops <strong>{$session_name}</strong> Tidak Perlu Ditolak Ulang!")
                ->body("Data Cuti Dengan Keperluan <b>$tidak_masuk->keperluan</b> Sudah Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            return redirect()->back();
        }
    }

    public function decline_force($tidak_masuk)
    {
        $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk)->with(['karyawan', 'approved', 'decline'])->first();

        if ($tidak_masuk->status != "decline") {
            $tidak_masuk->status = "decline";
            $tidak_masuk->decline_by = auth()->user()->id;
            $tidak_masuk->updated_at = now();

            $tidak_masuk->save();
            $tidak_masuk = Tidak_masuk::where('id', $tidak_masuk->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! Data cuti berhasil di tolak")
                ->body("Data Cuti <b>$tidak_masuk->keperluan</b> Yang Diajukan Oleh <b>{$tidak_masuk->karyawan->nama}</b> Ini Berhasil Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->send();
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda berhasil di tolak")
                ->body("Data <b>$tidak_masuk->keperluan</b> Anda Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($tidak_masuk->karyawan->user);
            event(new DatabaseNotificationsSent($tidak_masuk->karyawan->user));
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$tidak_masuk->karyawan->nama}</b> Data cuti anda berhasil di tolak")
                ->body("Data <b>$tidak_masuk->keperluan</b> Anda Di Tolak Oleh <b>{$tidak_masuk->decline->name}</b> Pada <b>{$tidak_masuk->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($tidak_masuk) {
                            return TidakMasukResource::getUrl('edit', ['record' => $tidak_masuk]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($tidak_masuk->karyawan->user);
            return redirect()->back();
        }
    }
}