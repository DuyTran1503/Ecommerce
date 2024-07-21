<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Address;
use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Checkout Page')]
class CheckoutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function handleClick()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
        ]);
        $cart_items = CartManagement::getCartItems();
        $line_items = [];
        foreach ($cart_items as $key => $value) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'vnd',
                    'unit_amount' => $value['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $value['name']
                    ]
                ],
                'quantity' => $value['quantity']

            ];
        }
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::grandTotalCartItem($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'vnd';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order place by ' . auth()->user()->name;

        $address = new Address();

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->address = $this->address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $redirect_url = '';

        if ($this->payment_method == 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $sessionCheckout = Session::create([
                'pm_types' => ['card'],
                'customer' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);
            $redirect_url =  $sessionCheckout->url;
        } else {
            $redirect_url =   route('success');
        }
        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartManagement::clearCartItems();
        return redirect($redirect_url);
    }
    public function render()
    {
        $cart_items = CartManagement::getCartItems();
        $grand_total = CartManagement::grandTotalCartItem($cart_items);
        // $address = Address::query()->get();
        // dd($cart_items);

        return view('livewire.checkout-page', compact('cart_items', 'grand_total'));
    }
}
