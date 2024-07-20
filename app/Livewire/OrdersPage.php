<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order page - Septenary Solution')]
class OrdersPage extends Component
{
    public function render()
    {
        return view('livewire.orders-page');
    }
}
