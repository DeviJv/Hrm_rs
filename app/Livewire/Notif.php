<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Notif extends Component
{
    public function render()
    {
        if (auth()->user() != null) {
            return view('notif', ['user' => auth()->user()->id]);
        }
        return view('blank');
    }

    // #[On('database-notifications.sent')]

}
