<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Kontrak;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\KontrakResource;
use App\Filament\Resources\StrsipResource;
use App\Models\Strsip;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class Reminderstrsip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-strsip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalan Pengingat Untuk masa berlaku STR & SIP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = Reminder::where('remindable_type', Strsip::class)->where('pengingat', Carbon::now()->format('Y-m-d'))
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
                ->iconColor('warning')
                ->persistent()
                ->body(" Sepertinya YTH.<b>{$reminder->karyawan->nama}</b> masa berlaku <b>STR/SIP</b> nya akan habis pada <b>" . $date . "</b> ")
                ->actions([
                    Action::make('Lihat')
                        ->url(StrsipResource::getUrl('edit', ['record' => $reminder->remindable->id])),
                ])
                ->sendToDatabase($reminder->user);
            Notification::make()
                ->title("Hai <b>{$reminder->user->name}</b>")
                ->icon('heroicon-o-exclamation-triangle')
                ->warning()
                ->duration(10000)
                ->iconColor('warning')
                ->persistent()
                ->body(" Sepertinya YTH.<b>{$reminder->karyawan->nama}</b> masa berlaku <b>STR/SIP</b> nya akan habis pada <b>" . $date . "</b> ")
                ->actions([
                    Action::make('Lihat')
                        ->url(StrsipResource::getUrl('edit', ['record' => $reminder->remindable->id])),
                ])
                ->broadcast($reminder->user);
            event(new DatabaseNotificationsSent($reminder->user));
        }
    }
}
