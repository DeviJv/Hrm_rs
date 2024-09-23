<?php

namespace App\Livewire;

use Livewire\Component;

class KwitansiBulk extends Component
{
    public $perusahaan;
    public $records;

    public static function test($perusahaan, $records)
    {
        return view('livewire.kwitansi-bulk');
    }
    public function render($perusahaan, $records)
    {
        return view('livewire.kwitansi-bulk');
    }
}
