<?php

namespace App\Filament\Resources\StrsipResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Filament\Resources\StrsipResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStrsip extends CreateRecord
{
    protected static string $resource = StrsipResource::class;


    protected function afterCreate(): void
    {
        $data = $this->record;
        $masa_berlaku_str = Carbon::parse($this->record->masa_berlaku_str);
        $masa_berlaku_sip = Carbon::parse($this->record->masa_berlaku_sip);

        $str = $data->reminder()->create([
            'pengingat' => $masa_berlaku_str->subMonth(6)->startOfMonth()->toDateString(),
            'karyawan_id' => $data->karyawan_id,
            'user_id' => auth()->user()->id,
            'sudah' => 0
        ]);
        $sip = $data->reminder()->create([
            'pengingat' => $masa_berlaku_sip->subMonth(6)->startOfMonth()->toDateString(),
            'karyawan_id' => $data->karyawan_id,
            'user_id' => auth()->user()->id,
            'sudah' => 0
        ]);
    }
}