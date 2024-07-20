<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Product Detail page - Septenary Solution')]
class ProductDetailPage extends Component
{
    use LivewireAlert;
    public $slug;
    public $quantity = 1;
    public function mount($slug)
    {
        $this->slug = $slug;
    }
    public function increaseQty()
    {
        $this->quantity++;
    }
    public function decreaseQty()
    {
        if ($this->quantity > 1) {

            $this->quantity--;
        }
    }
    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id, $this->quantity);
        $this->dispatch('update-cart-count', $total_count)->to(Navbar::class);
        $this->alert('success', 'Thêm giỏ hàng thành công', [
            'position' => 'top',
            'timer' => 2500,
            'toast' => true,
        ]);
    }
    public function render()
    {
        $product = Product::where('slug', $this->slug)->firstOrFail();
        return view('livewire.product-detail-page', compact('product'));
    }
}
