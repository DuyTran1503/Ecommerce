<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    public static function addItemToCart($product_id, $qty = 1)
    {
        $cart_items = self::getCartItems();
        $exist_item = null;
        foreach ($cart_items as $key => $value) {
            if ($value['product_id'] === $product_id) {
                $exist_item = $key;
                break;
            }
        }
        if ($exist_item !== null) {
            $cart_items[$exist_item]['quantity'] = $qty;
            // $cart_items[$exist_item]['total_amount'] = $cart_items[$exist_item];
            $cart_items[$exist_item]['total_amount'] = $qty * $cart_items[$exist_item]['unit_amount'];
            $cart_items[$exist_item]['unit_amount'];
        } else {
            $product = Product::where('id', $product_id)->first();
            if ($product) {
                $cart_items[] = [
                    'product_id' => $product_id,
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $qty,
                    'image' => $product->images[0],
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price,
                ];
            }
        }
        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    public static function removeCartItem($product_id)
    {
        $cart_items = self::getCartItems();
        foreach ($cart_items as $key => $value) {
            if ($value['product_id'] === $product_id) {
                unset($cart_items[$key]);
            }
        }
        self::addCartItemsToCookie($cart_items);
        return ($cart_items);
    }
    public static function addCartItemsToCookie($cart_items)
    {
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }
    public static function clearCartItems()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }
    public static function getCartItems()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        if (!$cart_items) {
            $cart_items = [];
        }
        return $cart_items;
    }
    public static function incrementQuantityToCartItem($product_id)
    {

        $cart_items = self::getCartItems();
        foreach ($cart_items as $key => $value) {
            if ($cart_items[$key]['product_id'] === $product_id) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }
        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }
    public static function decrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItems();
        foreach ($cart_items as $key => $value) {
            if ($cart_items[$key]['product_id'] === $product_id) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                }
            }
        }
        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    public static function grandTotalCartItem($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }
}
