<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Order page - Septenary Solution')]
class OrdersPage extends Component
{
    use WithPagination;
    public function render()
    {
        $orders = Order::where('user_id', auth()->user()->id)->latest()->paginate(10);
        return view('livewire.orders-page', compact('orders'));
    }
}
