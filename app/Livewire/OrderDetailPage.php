<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Title;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

#[Title('Order detail page - Septenary Solution')]
class OrderDetailPage extends Component
{
    public $order_id;
    public function mount($order_id)
    {
        $this->order_id = $order_id;
    }
    public function handleExport()
    {
        $data = $this->getDataForExport(); // Phương thức này cần được định nghĩa để lấy dữ liệu cần export
        $order_items = $data['order_items'];
        $address = $data['address'];
        $order = $data['order'];
        $pdf = PDF::loadView('livewire.export', compact('order_items', 'address', 'order'));

        return $pdf->download('livewire.export.pdf');
    }
    private function getDataForExport()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();
        $address = Address::where('order_id', $this->order_id)->first();
        $order = Order::where('id', $this->order_id)->first();
        return [
            'order_items' => $order_items,
            'address' => $address,
            'order' => $order
        ];
    }
    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();
        $address = Address::where('order_id', $this->order_id)->first();
        $order = Order::where('id', $this->order_id)->first();
        return view('livewire.order-detail-page', compact('order_items', 'address', 'order'));
    }
}
