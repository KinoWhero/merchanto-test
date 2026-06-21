<?php

use App\Contracts\CatalogInterface;
use App\Data\OrderedProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

new class extends Component {
    public int $orderId;
    public object $order;

    private function catalog(): CatalogInterface
    {
        return app(CatalogInterface::class);
    }

    public function mount(): void
    {
        $this->order = Order::where('id', $this->orderId)
            ->firstOrFail();
    }

    /**
     * @return OrderedProduct[]
     */
    private function orderedProducts(): array
    {
        return $this->order->items
            ->map(fn ($item): OrderedProduct => new OrderedProduct(
                id: (int) $item->product_id,
                categoryName: null,
                name: $item->product_name,
                description: '',
                price: (float) $item->unit_price,
                quantity: (int) $item->quantity,
            ))
            ->values()
            ->all();
    }

    public function confirmOrder(): void
    {
        if ($this->order->status !== OrderStatus::Pending) {
            $this->addError('order', 'Only pending orders can be confirmed.');

            return;
        }

        try {
            $this->catalog()->reduceStockOrFail($this->orderedProducts());
        } catch (\RuntimeException $exception) {
            $this->addError('order', $exception->getMessage());

            return;
        }

        DB::table('orders')
            ->where('id', $this->order->id)
            ->update([
                'status' => OrderStatus::Confirmed->value,
                'updated_at' => now(),
            ]);

        $this->order = Order::where('id', $this->orderId)
            ->firstOrFail();

        session()->flash('status', 'Order has been confirmed successfully.');
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
                <div class="flex flex-col items-end gap-3">
                    <span
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $order->status->badgeClasses() }}">
                        {{ $order->status->label() }}
                    </span>

                    @if ($order->status === \Modules\Order\Enums\OrderStatus::Pending)
                        <button
                            type="button"
                            wire:click="confirmOrder"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-950 transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Confirm order
                        </button>
                    @endif
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-800 bg-green-950/60 px-4 py-3 text-sm text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @error('order')
                <div class="mb-6 rounded-lg border border-red-800 bg-red-950/60 px-4 py-3 text-sm text-red-200">
                    {{ $message }}
                </div>
            @enderror

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
