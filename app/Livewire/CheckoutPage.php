<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
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
    public function mount()
    {
        $cart_items = CartManagement::getCartItems();
        if (count($cart_items) === 0) {
            return redirect('/products');
        }
    }
    public function handleClick()
    {
        // Validation remains the same
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
        $total_amount = 0;
        $stripe_max = 99999999; // 99,999,999 VND

        foreach ($cart_items as $key => $value) {
            $item_amount = $value['unit_amount'] * $value['quantity']; // Không nhân với 100
            $total_amount += $item_amount;

            $stripe_amount = min($item_amount, $stripe_max - array_sum(array_column($line_items, 'price_data.unit_amount')));

            if ($stripe_amount > 0) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'vnd',
                        'unit_amount' => $stripe_amount, // Đã là VND, không cần nhân 100
                        'product_data' => [
                            'name' => $value['name'] . ($stripe_amount < $item_amount ? ' (Partial payment)' : '')
                        ]
                    ],
                    'quantity' => 1
                ];
            }

            if (array_sum(array_column($line_items, 'price_data.unit_amount')) >= $stripe_max) {
                break;
            }
        }

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::grandTotalCartItem($cart_items);
        $order->payment_method = $this->payment_method;
        $order->status = 'new';
        $order->currency = 'vnd';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order placed by ' . auth()->user()->name;

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

            $stripe_total = array_sum(array_column($line_items, 'price_data.unit_amount'));
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
                'cancel_url' => route('cancel'),
            ]);
            $redirect_url = $sessionCheckout->url;

            // Update payment status
            $order->payment_status = $stripe_total < $total_amount ? 'partial' : 'pending';
        } else {
            $redirect_url = route('success');
            $order->payment_status = 'pending';
        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartManagement::clearCartItems();
        Mail::to(request()->user())->send(new OrderPlaced($order));
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
