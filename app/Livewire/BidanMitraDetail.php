<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BidanMitra;

class BidanMitraDetail extends Component
{
    public ?int $recordId = null;
    public ?BidanMitra $bidan    = null;

    protected $listeners = [
        'showBidanDetail' => 'show',
    ];

    public function showBidanDetail(int $id): void
    {
        $this->recordId = $id;
        $this->bidan    = BidanMitra::with('locations')->find($id);

        // Ini akan trigger <x-filament::modal id="custom-bidan-modal"> untuk buka
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'custom-bidan-modal',
        ]);
    }

    public function render()
    {
        return view('livewire.bidan-mitra-detail');
    }
}