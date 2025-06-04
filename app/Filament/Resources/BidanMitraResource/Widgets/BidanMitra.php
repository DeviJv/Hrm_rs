<?php

namespace App\Filament\Resources\BidanMitraResource\Widgets;

use App\Models\Location;
use App\Models\BidanMitra as BidanMitraModel;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Enums\MaxWidth;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\BidanMitraResource;
use App\Models\BidanMitra as ModelsBidanMitra;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\BidanMitraResource\Pages\ListBidanMitras;
use Filament\Forms\Components\TextInput;

class BidanMitra extends MapWidget
{
    use InteractsWithPageTable;
    protected static ?string $heading = 'Map';
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;
    protected static ?bool $clustering = true;
    protected static ?bool $fitToBounds = true;
    protected static ?int $zoom = 12;
    protected static bool $isLazy = false;
    protected $listeners = ['refreshMap' => '$refresh'];
    protected static ?string $mapId = 'bidanMap';

    protected function getTablePage(): string
    {
        return ListBidanMitras::class;
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function markerAction(): Action
    {
        return Action::make('markerAction')
            ->form([
                Section::make([
                    TextInput::make('nama'),
                ])
            ])
            ->record(function (array $arguments) {
                return array_key_exists('model_id', $arguments) ? BidanMitraModel::find($arguments['model_id']) : null;
            });
    }
    protected function getData(): array
    {
        $bidanMitras = $this->getPageTableQuery()->get();

        $data = [];

        foreach ($bidanMitras as $bidan) {
            foreach ($bidan->locations as $location) {
                $data[] = [
                    'id' => $location->id,
                    'location' => [
                        'lat' => round((float) $location->lat, static::$precision),
                        'lng' => round((float) $location->lang, static::$precision),
                    ],
                    'label' => $bidan->nama,
                    'custom_id' => $bidan->id,
                    // 'info' => "<button onclick=\"window.openRecordModal({$bidan->id})\" style='text-decoration: underline; color: blue;'>📄 Lihat Detail</button>",
                    'info' => '<button class="popup-detail" data-id="' . $bidan->id . '">📍 Lihat Detail</button>',

                    'icon' => [
                        'url' => match ($bidan->status_kerja_sama) {
                            'sudah' => 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            'belum' => 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                            default => 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                        },
                        'type' => 'png',
                        'scale' => [1, 1], // PNG ngga perlu scaling seperti SVG
                    ],
                ];
            }
        }

        return $data;
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}