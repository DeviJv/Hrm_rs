<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Kontrak;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\KontrakResource;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class ReminderKontrak extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-kontrak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalan Pengingat Untuk Kontrak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = Reminder::where('remindable_type', Kontrak::class)
            ->whereMonth('pengingat', '=', date('m', strtotime(now())))
            ->where('sudah', false)->get();

        foreach ($reminders as $reminder) {
            $date = Carbon::parse($reminder->pengingat)->addMonth(3)->format('d/m/Y');
            $reminder->sudah = true;
            $reminder->save();
            Notification::make()
                ->title("Hai <b>{$reminder->user->name}</b>")
                ->icon('heroicon-o-exclamation-triangle')
                ->warning()
                ->duration(10000)
                ->iconColor('danger')
                ->persistent()
                ->body(" Sepertinya YTH.<b>{$reminder->karyawan->nama}</b> akan habis kontrak pada <b>" . $date . "</b> ")
                ->actions([
                    Action::make('Lihat')
                        ->url(KontrakResource::getUrl('edit', ['record' => $reminder->remindable->id])),
                ])
                ->sendToDatabase($reminder->user);
            Notification::make()
                ->title("Hai <b>{$reminder->user->name}</b>")
                ->icon('heroicon-o-exclamation-triangle')
                ->warning()
                ->duration(10000)
                ->iconColor('danger')
                ->persistent()
                ->body(" Sepertinya YTH.<b>{$reminder->karyawan->nama}</b> akan habis kontrak pada <b>" . $date . "</b> ")
                ->actions([
                    Action::make('Lihat')
                        ->url(KontrakResource::getUrl('edit', ['record' => $reminder->remindable->id])),
                ])
                ->broadcast($reminder->user);
            event(new DatabaseNotificationsSent($reminder->user));
        }
    }
}