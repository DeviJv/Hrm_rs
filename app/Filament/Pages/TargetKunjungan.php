<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\BidanMitra;

class TargetKunjungan extends Page {
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Marketing';
    protected static string $view = 'filament.pages.target-kunjungan';

    public $markers = [];

    public function mount() {
        $this->markers = BidanMitra::with('locations')->get()
            ->flatMap(function ($bidan) {
                return $bidan->locations->map(function ($loc) use ($bidan) {
                    return [
                        'id' => $loc->id,
                        'lat' => (float) $loc->lat,
                        'lng' => (float) $loc->lang,
                        'nama' => $bidan->nama,
                        'status' => $bidan->status_kerja_sama,
                        'info' => '<div class="p-2"><strong>' . $bidan->nama . '</strong><br><a href="/bidan-mitras/' . $bidan->id . '">Detail</a></div>',
                    ];
                });
            })->values()->toArray();
    }
}