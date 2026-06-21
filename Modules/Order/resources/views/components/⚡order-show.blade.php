<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Order\Models\Order;

new class extends Component {
    public int $orderId;
    public object $order;

    public function mount(): void
    {
        $this->order = Order::where('id', $this->orderId)
            ->firstOrFail();
    }
};
?>

<div>
    <div class="min-h-screen bg-gray-950 px-6 py-10 text-gray-100">
        <div class="mx-auto max-w-5xl">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-400">Order #{{ $order->id }}</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Order details</h1>
                    <p class="mt-2 text-sm text-gray-400">
                        View customer information, selected products, quantities, and order status.
                    </p>
                </div>

                <span
                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $order->status->badgeClasses() }}">
                    {{ $order->status->label() }}
                </span>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                    <div class="border-b border-gray-800 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Order items</h2>
                    </div>

                    <div class="divide-y divide-gray-800">
                        @foreach ($order->items as $item)
                            <div class="px-6 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-medium text-white">{{ $item->product_name }}</div>
                                        <div class="mt-1 text-xs text-gray-400">
                                            USD {{ number_format((float) $item->unit_price, 2) }}
                                            × {{ $item->quantity }}
                                        </div>
                                    </div>

                                    <div class="text-sm font-semibold text-gray-100">
                                        USD {{ number_format((float) $item->total_price, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-800 px-6 py-4">
                        <div class="flex items-center justify-between text-base font-semibold text-white">
                            <span>Total</span>
                            <span>USD {{ number_format((float) $order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                    <div class="border-b border-gray-800 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Customer info</h2>
                    </div>

                    <div class="space-y-4 px-6 py-5 text-sm">
                        <div>
                            <div class="text-gray-400">Name</div>
                            <div class="mt-1 font-medium text-white">{{ $order->customer_name }}</div>
                        </div>

                        <div>
                            <div class="text-gray-400">Email</div>
                            <div class="mt-1 text-white">{{ $order->customer_email }}</div>
                        </div>

                        @if ($order->customer_phone)
                            <div>
                                <div class="text-gray-400">Phone</div>
                                <div class="mt-1 text-white">{{ $order->customer_phone }}</div>
                            </div>
                        @endif

                        @if ($order->customer_address)
                            <div>
                                <div class="text-gray-400">Address</div>
                                <div class="mt-1 text-white">{{ $order->customer_address }}</div>
                            </div>
                        @endif

                        <div>
                            <div class="text-gray-400">Created</div>
                            <div class="mt-1 text-white">
                                {{ Carbon::parse($order->created_at)->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
