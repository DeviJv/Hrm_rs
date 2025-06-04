<?php

namespace App\Filament\Resources\BidanMitraResource\Pages;

use Filament\Actions;
use App\Models\Location;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BidanMitraResource;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;

class EditBidanMitra extends EditRecord {
    use InteractsWithMaps;
    protected static string $resource = BidanMitraResource::class;
    protected static ?string $title = 'Ubah Mitra';

    protected function getHeaderActions(): array {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function afterSave(): void {
        $lat = $this->data['location']['lat'] ?? null;
        $lng = $this->data['location']['lng'] ?? null;
        if (! $lat || ! $lng) {
            return;
        }
        // Buat lokasi baru
        $location = Location::create([
            'lat' => $lat,
            'lang' => $lng,
        ]);
        // Attach lokasi ke bidan
        $this->record->locations()->sync([$location->id]);
    }
}
