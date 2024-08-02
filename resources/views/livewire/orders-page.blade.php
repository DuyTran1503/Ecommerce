<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <h1 class="text-4xl font-bold text-slate-500">My Orders</h1>
    <div class="flex flex-col bg-white p-5 rounded mt-4 shadow-lg">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">STT</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Payment
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order
                                    Amount</th>
                                <th scope="col"
                                    class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $item)
                                @php
                                    $status = '';
                                    switch ($item->status) {
                                        case 'new':
                                            $statusClass = 'bg-blue-500';
                                            $statusText = 'New';
                                            break;
                                        case 'processing':
                                            $statusClass = 'bg-yellow-500';
                                            $statusText = 'Processing';
                                            break;
                                        case 'shipped':
                                            $statusClass = 'bg-purple-500';
                                            $statusText = 'Shipped';
                                            break;
                                        case 'delivered':
                                            $statusClass = 'bg-green-500';
                                            $statusText = 'Delivered';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'bg-red-500';
                                            $statusText = 'Cancelled';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-500';
                                            $statusText = ucfirst($item->status);
                                    }
                                    $status = "<span class='{$statusClass} py-1 px-3 rounded text-white shadow'>{$statusText}</span>";
                                @endphp
                                <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-slate-900 dark:even:bg-slate-800"
                                    wire:key='{{ $item->id }}'>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $key + 1 }}</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $item->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ $item->created_at->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{-- <span
                                            class="bg-orange-500 py-1 px-3 rounded text-white shadow">{{ $item->status }}
                                        </span> --}}
                                        {!! $status !!}
                                    </td>
                                    @php
                                        $payment_status = '';
                                        switch ($item->payment_status) {
                                            case 'pending':
                                                $statusClass = 'bg-yellow-500';
                                                $statusText = 'Pending';
                                                break;
                                            case 'paid':
                                                $statusClass = 'bg-green-500';
                                                $statusText = 'Paid';
                                                break;
                                            case 'failed':
                                                $statusClass = 'bg-red-500';
                                                $statusText = 'Failed';
                                                break;
                                            default:
                                                $statusClass = 'bg-green-500';
                                                $statusText = ucfirst($item->payment_status);
                                        }
                                        $payment_status = "<span class='{$statusClass} py-1 px-3 rounded text-white shadow'>{$statusText}</span>";
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {!! $payment_status !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ Number::currency($item->grand_total, 'VND') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        <a href="/orders/{{ $item->id }}"
                                            class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-900">View
                                            Details</a>
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                        <div class="flex justify-end mt-6">
                            <nav aria-label="page-navigation">
                                <ul class="flex list-style-none">
                                    @if (!empty($orders))
                                        {{ $orders->links() }}
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
