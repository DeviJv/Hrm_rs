<?php

namespace App\Filament\Resources\BidanMitraResource\Pages;

use App\Filament\Resources\BidanMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBidanMitra extends CreateRecord {
    protected static string $resource = BidanMitraResource::class;
    protected static ?string $title = 'Buat Mitra Baru';

    public function afterCreate(): void {
        $this->record->locations()->create([
            'lat' => $this->data['location']['lat'],
            'lang' => $this->data['location']['lng'],
        ]);
    }
}
