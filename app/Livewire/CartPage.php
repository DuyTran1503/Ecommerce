<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use Livewire\Attributes\Title;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CartPage extends Component
{
    use LivewireAlert;
    #[Title('Cart')]
    public $cart_items = [];
    public $grand_total;
    public $quantity = 1;
    public function mount()
    {
        $this->cart_items = CartManagement::getCartItems();
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
    }
    public function removeItem($product_id)
    {
        $this->cart_items = CartManagement::removeCartItem($product_id);
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->dispatch('update-cart-count', total_count: count($this->cart_items))->to(Navbar::class);
        $this->alert('success', 'Bạn đã xóa sản phẩm thành công', [
            'position' => 'top',
            'timer' => 2500,
            'toast' => true,
        ]);
    }
    public function increaseQty($product_id)
    {
        $this->cart_items = CartManagement::incrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
    }
    public function decreaseQty($product_id)
    {
        if (!empty($this->cart_items)) {

            $this->cart_items = CartManagement::decrementQuantityToCartItem($product_id);
            $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
        }
    }
    public function render()
    {
        return view('livewire.cart-page');
    }
}
