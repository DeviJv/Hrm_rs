<?php

namespace App\Livewire;

use Livewire\Component;

class ShowPasienModal extends Component {
    public bool $open = false;
    public ?int $bidanId = null;
    public ?string $type = null;

    protected $listeners = ['tampilkan-kunjungan-modal' => 'bukaModal'];

    public function bukaModal($bidanId, $type) {

        $this->bidanId = (int) $bidanId;
        $this->type = (string) $type;
        $this->dispatch('open-modal', id: 'kunjunganModal');
    }

    public function closeModal() {
        $this->open = false;
    }

    public function render() {
        return view('livewire.show-pasien-modal');
    }
}