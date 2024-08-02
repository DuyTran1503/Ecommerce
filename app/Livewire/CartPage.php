<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Cupon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CartPage extends Component
{
    use LivewireAlert;
    #[Title('Cart')]
    public $cart_items = [];
    public $grand_total;
    public $total;
    public $quantity = 1;
    public $code = '';
    public $cupon = [];

    public function mount()
    {
        $this->cart_items = CartManagement::getCartItems();
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->code = '';
    }
    public function removeItem($product_id)
    {
        $this->cart_items = CartManagement::removeCartItem($product_id);
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->total = CartManagement::grandTotalCartItem($this->cart_items);
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
        $this->total = CartManagement::grandTotalCartItem($this->cart_items);
    }
    public function decreaseQty($product_id)
    {
        if (!empty($this->cart_items)) {

            $this->cart_items = CartManagement::decrementQuantityToCartItem($product_id);
            $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
            $this->total = CartManagement::grandTotalCartItem($this->cart_items);
        }
    }
    public function handleCupon()
    {
        $this->validate([
            'code' => 'required',
        ]);

        $this->cupon = Cupon::query()
            ->where('code', $this->code)
            ->where('is_active', 1)
            ->first();
        if (!$this->cupon) {
            $this->alert('error', 'Mã khuyến mại đã hết hạn', [
                'position' => 'top',
                'timer' => 2500,
                'toast' => true,
            ]);
            return;
        }
        $this->total = CartManagement::grandTotalCartItem($this->cart_items) - $this->cupon->cupon;
        setcookie('total', $this->total, time() + (86400 * 30), "/");
    }

    public function handleReset()
    {
        $this->resetErrorBag();
        $this->reset('code');
        $this->code = '';
        $this->grand_total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->total = CartManagement::grandTotalCartItem($this->cart_items);
        $this->cupon = '';
        setcookie('total', '', time() - 0, '/');
        $this->dispatch('input-updated');
    }

    public function render()
    {
        $code = $this->code;
        return view('livewire.cart-page', compact('code'));
    }
}
