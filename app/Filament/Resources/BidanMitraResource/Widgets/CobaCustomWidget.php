<?php

namespace App\Filament\Resources\BidanMitraResource\Widgets;

use App\Models\BidanMitra;
use Filament\Widgets\Widget;

class CobaCustomWidget extends Widget
{
    protected static string $view = 'filament.resources.bidan-mitra-resource.widgets.coba-custom-widget';

    public function getViewData(): array
    {
        return [
            'markers' => $this->getMarkers(), // ini yang akan dipanggil di Blade
        ];
    }

    public function getMarkers(): array
    {
        return BidanMitra::with('locations')->get()->flatMap(function ($bidan) {
            return $bidan->locations->map(function ($loc) use ($bidan) {
                return [
                    'lat' => (float) $loc->lat,
                    'lng' => (float) $loc->lang,
                    'nama' => $bidan->nama,
                    'url' => route('filament.admin.resources.bidan-mitras.view', ['record' => $bidan]),
                ];
            });
        })->toArray();
    }
}