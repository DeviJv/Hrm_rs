<?php

namespace App\Filament\Resources\KontrakResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\KontrakResource;

class CreateKontrak extends CreateRecord
{
    protected static string $resource = KontrakResource::class;

    protected function afterCreate(): void
    {
        $data = $this->record;
        $tgl_kontrak = Carbon::parse($this->record->tgl_akhir);

        $kontrak = $data->reminder()->create([
            'pengingat' => $tgl_kontrak->subMonth()->startOfMonth()->toDateString(),
            'karyawan_id' => $data->karyawan_id,
            'user_id' => auth()->user()->id,
            'sudah' => 0
        ]);
    }
}
