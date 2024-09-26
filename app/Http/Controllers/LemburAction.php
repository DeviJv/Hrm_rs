<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;
use App\Filament\Resources\LemburResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class LemburAction extends Controller
{

    public function approve($lembur)
    {
        $session_name = auth()->user()->name;
        $lembur = Lembur::where('id', $lembur)->with(['karyawan', 'approved', 'decline'])->first();
        if ($lembur == null) {
            Notification::make()
                ->danger()
                ->title("Opps data lembur tidak di temukan!")
                ->send();
            return redirect()->back();
        }
        if ($lembur->status == "decline") {
            Notification::make()
                ->warning()
                ->title("Opps data pengajuan lembur pada tanggal <b>{$lembur->tgl_lembur}</b> yang di ajukan oleh <b>{$lembur->karyawan->nama}</b> sudah ditolak oleh <b>{$lembur->decline->name}</b>!")
                ->body('Apakah anda yakin ingin melakukan perubahan ini?')
                ->actions([
                    Action::make('setujui_saja')
                        ->label('Ah Setujui Sajalah')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('lembur.approve.force', $lembur)),
                    Action::make('tolak_saja')
                        ->label('Eh yaudah deh')
                        ->color('danger')
                        ->markAsRead()
                    // ->url(fn() => route('cuti.approve.force', $tidak_masuk)),
                ])
                ->send();
            return redirect()->back();
        }
        if ($lembur->status != "approved") {
            $lembur->status = "approved";
            $lembur->approved_by = auth()->user()->id;
            $lembur->updated_at = now();
            $lembur->save();
            $lembur = Lembur::where('id', $lembur->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! data lembur berhasil di setujui")
                ->body("Data lembur <b>{$lembur->tgl_lembur}</b> <b>{$lembur->karyawan->nama}</b> ini berhasil di setujui oleh <b>{$lembur->approved->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->send();
            Notification::make()
                ->success()
                ->title("Selamat <b>{$lembur->karyawan->nama}</b> Data lembur anda berhasil di setujui")
                ->body("Data pangajuan lembur pada tanggal <b>{$lembur->tgl_lembur}</b> anda berhasil di setujui oleh <b>{$lembur->approved->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($lembur->karyawan->user);

            event(new DatabaseNotificationsSent($lembur->karyawan->user));
            Notification::make()
                ->success()
                ->title("Selamat <b>{$lembur->karyawan->nama}</b> data lembur anda berhasil di setujui")
                ->body("Data pangajuan lembur pada tanggal <b>{$lembur->tgl_lembur}</b> Anda Berhasil Di Setujui Oleh <b>{$lembur->approved->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($lembur->karyawan->user);
            return redirect()->back();
        }
        if ($lembur->status == "approved") {
            Notification::make()
                ->danger()
                ->title("Ops <strong>{$session_name}</strong> Tidak Perlu Persetujuan Ulang!")
                ->body("Data lembur pada tanggal <b>{$lembur->tgl_mulai}</b> ini sudah di setujui oleh <b>{$lembur->approved->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->send();
            return redirect()->back();
        }
    }

    public function approve_force($lembur)
    {
        $lembur = Lembur::where('id', $lembur)->with(['karyawan', 'approved', 'decline'])->first();

        if ($lembur->status != "approved") {
            $lembur->status = "approved";
            $lembur->approved_by = auth()->user()->id;
            $lembur->updated_at = now();

            $lembur->save();
            $lembur = Lembur::where('id', $lembur->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! data lembur berhasil di setujui")
                ->body("Data lembur <b>{$lembur->tgl_lembur}</b> <b>{$lembur->karyawan->nama}</b> ini berhasil di setujui oleh <b>{$lembur->approved->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->send();
            Notification::make()
                ->success()
                ->title("Selamat <b>{$lembur->karyawan->nama}</b> Data lembur anda berhasil di setujui")
                ->body("Data pangajuan lembur pada tanggal <b>{$lembur->tgl_lembur}</b> anda berhasil di setujui oleh <b>{$lembur->approved->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($lembur->karyawan->user);

            event(new DatabaseNotificationsSent($lembur->karyawan->user));
            Notification::make()
                ->success()
                ->title("Selamat <b>{$lembur->karyawan->nama}</b> data lembur anda berhasil di setujui")
                ->body("Data pangajuan lembur pada tanggal <b>{$lembur->tgl_lembur}</b> Anda Berhasil Di Setujui Oleh <b>{$lembur->approved->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($lembur->karyawan->user);
            return redirect()->back();
        }
    }


    public function decline($lembur)
    {
        $session_name = auth()->user()->name;

        $lembur = Lembur::where('id', $lembur)->with(['karyawan', 'approved', 'decline'])->first();
        if ($lembur == null) {
            Notification::make()
                ->danger()
                ->title("Opps data lembur tidak di temukan!")
                ->send();
            return redirect()->back();
        }
        if ($lembur->status == "approved") {
            Notification::make()
                ->warning()
                ->title("Opps data pengajuan lembur ini sudah disetujui Oleh <b>{$lembur->approved->name}</b> Pada <b>{$lembur->updated_at}</b>!")
                ->actions([
                    Action::make('tolak_saja')
                        ->label('Ah ga boleh pokonya mah 😤')
                        ->color('success')
                        ->markAsRead()
                        ->url(fn() => route('lembur.decline.force', $lembur)),
                    Action::make('tolak_saja')
                        ->label('Eh yaudah deh')
                        ->color('danger')
                        ->markAsRead()
                    // ->url(fn() => route('cuti.approve.force', $tidak_masuk)),
                ])
                ->send();
            return redirect()->back();
        }
        if ($lembur->status != "decline") {
            $lembur->status = "decline";
            $lembur->decline_by = auth()->user()->id;
            $lembur->updated_at = now();
            $lembur->save();
            $lembur = Lembur::where('id', $lembur->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! data lembur berhasil di tolak!")
                ->body("Data lembur pada tanggal <b>{$lembur->tgl_lembur}</b> Yang Diajukan  <b>{$lembur->karyawan->nama}</b> Ini Berhasil Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->send();
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$lembur->karyawan->nama}</b> Data cuti anda di tolak")
                ->body("Data <b>{$lembur->tgl_lembur}</b> Anda Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($lembur->karyawan->user);
            event(new DatabaseNotificationsSent($lembur->karyawan->user));
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$lembur->karyawan->nama}</b> Data lembur anda di tolak")
                ->body("Data <b>{$lembur->tgl_lembur}</b> Anda Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($lembur->karyawan->user);
            return redirect()->back();
        }
        if ($lembur->status == "decline") {
            Notification::make()
                ->danger()
                ->title("Ops <strong>{$session_name}</strong> Tidak Perlu Ditolak Ulang!")
                ->body("Data lembur pada tanggal <b>{$lembur->tgl_lembur}</b> sudah di tolak oleh <b>{$lembur->decline->name}</b> pada <b>{$lembur->updated_at}</b>.")
                ->send();
            return redirect()->back();
        }
    }

    public function decline_force($lembur)
    {
        $lembur = Lembur::where('id', $lembur)->with(['karyawan', 'approved', 'decline'])->first();

        if ($lembur->status != "decline") {
            $lembur->status = "decline";
            $lembur->decline_by = auth()->user()->id;
            $lembur->updated_at = now();

            $lembur->save();
            $lembur = Lembur::where('id', $lembur->id)->with(['karyawan', 'approved', 'decline'])->first();
            Notification::make()
                ->success()
                ->title("Selamat! data lembur berhasil di tolak!")
                ->body("Data lembur pada tanggal <b>{$lembur->tgl_lembur}</b> Yang Diajukan  <b>{$lembur->karyawan->nama}</b> Ini Berhasil Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->send();
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$lembur->karyawan->nama}</b> Data cuti anda di tolak")
                ->body("Data <b>{$lembur->tgl_lembur}</b> Anda Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->sendToDatabase($lembur->karyawan->user);
            event(new DatabaseNotificationsSent($lembur->karyawan->user));
            Notification::make()
                ->danger()
                ->title("Maaf <b>{$lembur->karyawan->nama}</b> Data lembur anda di tolak")
                ->body("Data <b>{$lembur->tgl_lembur}</b> Anda Di Tolak Oleh <b>{$lembur->decline->name}</b> Pada <b>{$lembur->updated_at}</b>.")
                ->actions([
                    Action::make('detail')
                        ->label('Lihat Detail!')
                        ->url(function () use ($lembur) {
                            return LemburResource::getUrl('edit', ['record' => $lembur]);
                        })
                        ->markAsRead()
                        ->color('info'),
                ])
                ->broadcast($lembur->karyawan->user);
            return redirect()->back();
        }
    }
}
