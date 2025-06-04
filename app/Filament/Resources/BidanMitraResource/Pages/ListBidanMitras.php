<?php

namespace App\Filament\Resources\BidanMitraResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BidanMitraResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;
use App\Filament\Resources\BidanMitraResource\Widgets\BidanMitra;
use App\Filament\Resources\BidanMitraResource\Widgets\CobaCustomWidget;
use App\Filament\Resources\BidanMitraResource\Widgets\CustomBidanWidget;
use App\Filament\Resources\BidanMitraResource\Widgets\BidanMitraTableWidget;

class ListBidanMitras extends ListRecords
{
    use ExposesTableToWidgets, InteractsWithMaps;
    protected static string $resource = BidanMitraResource::class;
    protected $listeners = ['openBidanModal'];

    public function openBidanModal($id)
    {
        $this->dispatch('openModal', 'filament.resources.bidan-mitras.view', [
            'recordId' => $id,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // BidanMitra::class,
            // CobaCustomWidget::class
            // BidanMitraTableWidget::class,
            CustomBidanWidget::class
        ];
    }
}