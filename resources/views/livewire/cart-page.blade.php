<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-2xl font-semibold mb-4">Shopping Cart</h1>
            <form wire:submit.prevent='handleCupon' class="flex gap-[12px] items-end justify-center">
                @csrf
                <div>

                    <label class="block text-gray-700 dark:text-white mb-1" for="code">
                        Mã giảm giá
                    </label>
                    <input wire:model='code' x-data="{}" x-on:input-updated.window="$el.value = ''"
                        class="rounded-lg border p-[9px] dark:bg-gray-700 dark:text-white dark:border-none {{ $code ? 'bg-gray-100' : '' }}"
                        id="code" type="text" {{ $code ? 'disabled' : '' }}>
                    @error('code')
                        <p class=" text-xs text-red-600 mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="">
                    <button type="submit"
                        class="bg-blue-400 border-2 mb-[5px]
                                                 border-blue-400  rounded-lg
                                                  px-3 py-1 hover:bg-blue-800
                                                   hover:text-white hover:border-blue-800">
                        <span wire:loading.remove> Confirm </span>
                        <span wire:loading>Loading...</span>
                    </button>
                    <button type="button" wire:click='handleReset'
                        class="bg-white-400 border-2 mb-[5px]
                                                 border-cyan-700  rounded-lg
                                                  px-3 py-1 hover:bg-cyan-700
                                                   hover:text-white hover:border-cyan-700">
                        <span wire:loading.remove> Reset </span>
                        <span wire:loading>Loading...</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
                <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left font-semibold">Product</th>
                                <th class="text-left font-semibold">Price</th>
                                <th class="text-left font-semibold">Quantity</th>
                                <th class="text-left font-semibold">Total</th>
                                <th class="text-left font-semibold">Remove</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($cart_items as $item)
                                <tr wire:key={{ $item['product_id'] }}>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <img class="h-16 w-16 mr-4" src="{{ url('storage', $item['image']) }}"
                                                alt="Product image">
                                            <span class="font-semibold">{{ $item['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4">{{ Number::currency($item['unit_amount'], 'VND') }}</td>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <button wire:click='decreaseQty({{ $item['product_id'] }})'
                                                class="border rounded-md py-2 px-4 mr-2">-</button>
                                            <span class="text-center w-8">{{ $item['quantity'] }}</span>
                                            <button wire:click='increaseQty({{ $item['product_id'] }})'
                                                class="border rounded-md py-2 px-4 ml-2">+</button>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        @if (is_array($item['total_amount']))
                                            {{ Number::currency($item['unit_amount'] * $item['quantity'], 'VND') }}
                                        @else
                                            {{ Number::currency($item['total_amount'], 'VND') }}
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click='removeItem({{ $item['product_id'] }})'
                                            class="bg-slate-300 border-2
                                             border-slate-400 rounded-lg
                                              px-3 py-1 hover:bg-red-500
                                               hover:text-white hover:border-red-700">

                                            <span wire:loading.remove
                                                wire:target='removeItem({{ $item['product_id'] }})'>Remove</span><span
                                                wire:loading
                                                wire:target='removeItem({{ $item['product_id'] }})'>Removing...</span></button>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td>No items available in cart!</td>
                                </tr>
                            @endforelse
                            <!-- More product rows -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="md:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Summary</h2>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span> {{ Number::currency($grand_total, 'VND') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Discount</span>
                        <span>{{ Number::currency($cupon->cupon ?? 0, 'VND') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Shipping</span>

                        <span>{{ Number::currency(0, 'VND') }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Total</span>
                        <span class="font-semibold">{{ Number::currency($total, 'VND') }}</span>
                    </div>
                    @if ($cart_items)
                        <a href="/checkout" wire:navigate
                            class="bg-blue-500 block text-center text-white py-2 px-4 rounded-lg mt-4 w-full">
                            <span wire:loading.remove>Checkout</span>
                            <span wire:loading>Loading...</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
