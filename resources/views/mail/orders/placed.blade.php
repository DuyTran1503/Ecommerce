<x-mail::message>
    # Order palce successfully

    Thank you for your order. Your order number is: {{ $order->id }}

    <x-mail::button :url="$url">
        View Order
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
