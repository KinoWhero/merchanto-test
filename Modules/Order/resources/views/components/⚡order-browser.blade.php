<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Order\Models\Order;

new class extends Component {
    use WithPagination;

    public int $perPage = 10;

    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        return Order::query()
            ->orderBy('orders.updated_at')
            ->paginate($this->perPage);
    }
};
?>
<div>
    <div class="min-h-screen bg-gray-950 px-6 py-10 text-gray-100">
        <div class="mx-auto max-w-6xl">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-400">Order</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Orders</h1>
                    <p class="mt-2 text-sm text-gray-400">
                        Browse available orders from the Order module.
                    </p>
                </div>

                <a
                    href="{{ route('order.create') }}"
                    class="inline-flex items-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-950 transition hover:bg-amber-400"
                >
                    + Add order
                </a>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                <div class="border-b border-gray-800 px-6 py-4">
                    <h2 class="text-base font-semibold text-white">Orders list</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800 text-sm">
                        <thead
                            class="bg-gray-900/80 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4">Date</th>
                            <th scope="col" class="px-6 py-4">Customer</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4">Amount</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-800">
                        @forelse ($this->orders as $order)
                            <tr class="transition hover:bg-gray-800/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $order->updated_at }}</div>
                                </td>

                                <td class="px-6 py-4 text-gray-300">
                                    {{ $order->customer_name }}
                                    <small class="text-gray-400">({{ $order->customer_email }})</small>
                                </td>

                                <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $order->status->badgeClasses() }}">
                                    {{ $order->status->label() }}
                                </span>

                                <td class="px-6 py-4 font-medium text-gray-100">
                                    USD {{ number_format((float) $order->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                    No orders available yet.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-800 px-6 py-4">
                    {{ $this->orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
