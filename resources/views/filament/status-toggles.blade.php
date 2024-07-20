<div class="flex space-x-2">
    @php
        $columns = [
            'is_stock' => 'Stock',
            'is_active' => 'Active',
            'is_featured' => 'Featured',
            'on_sale' => 'On Sale',
        ];
    @endphp

    @foreach ($columns as $column => $label)
        <div>
            {{ $getRecord()->$column ? '✅' : '❌' }}

            <span class="sr-only">{{ $label }}</span>
        </div>
    @endforeach
</div>
