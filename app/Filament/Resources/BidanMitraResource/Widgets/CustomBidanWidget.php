<?php

namespace App\Filament\Resources\BidanMitraResource\Widgets;

use App\Models\BidanMitra;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\BidanMitraResource\Pages\ListBidanMitras;

class CustomBidanWidget extends Widget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '5s';  // polling 5 detik
    protected static bool $isLazy = false;
    protected static string $view = 'filament.resources.bidan-mitra-resource.widgets.custom-bidan-widget';

    public array $markers = [];

    public function mount(): void
    {
        $this->loadMarkers();
    }

    public function loadMarkers(): void
    {
        $this->markers = $this->getPageTableQuery()
            ->with('locations')
            ->get()
            ->flatMap(fn($bidan) => $bidan->locations->map(fn($loc) => [
                'id'     => $bidan->id,
                'lat'    => (float) $loc->lat,
                'lng'    => (float) $loc->lang,
                'nama'   => $bidan->nama,
                'status' => $bidan->status_kerja_sama,
                'url'    => route('filament.admin.resources.bidan-mitras.view', $bidan),
                'info'   => view('map-info', [
                    'bidan' => $bidan,
                    'loc'   => $loc,
                    'url'   => route('filament.admin.resources.bidan-mitras.view', $bidan),
                ])->render(),
            ]))
            ->toArray();
    }

    // dipanggil otomatis sesuai pollingInterval
    public function updateMarkers(): void
    {
        $this->loadMarkers();
    }

    protected function getTablePage(): string
    {
        return ListBidanMitras::class;
    }

    public function getColumnSpan(): string|int|array
    {
        return 'full';
    }
}