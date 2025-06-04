<?php

namespace App\Filament\Resources\BidanMitraResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\BidanMitraResource;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;

class ViewBidanMitra extends ViewRecord {
    use InteractsWithMaps;
    protected static string $resource = BidanMitraResource::class;
    protected static ?string $title = 'Lihat Mitra';

    public static function modalId(): string {
        return 'view-bidan-mitra';
    }
    protected function getHeaderActions(): array {
        return [
            Actions\EditAction::make(),
        ];
    }
}
