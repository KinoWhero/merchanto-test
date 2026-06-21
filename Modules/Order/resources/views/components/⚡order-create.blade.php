<?php

use App\Contracts\CatalogInterface;
use App\Data\OrderedProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Order\Enums\OrderStatus;

new class extends Component {

    public string $customerName = '';

    public string $customerEmail = '';

    public ?string $customerPhone = null;

    public ?string $customerAddress = null;

    /**
     * @var array<int, array{id: int, categoryName: ?string, name: string, description: string, price: float, availableQuantity: int, quantity: int}>
     */
    public array $items = [];

    #[Computed]
    public function products(): Collection
    {
        return $this->catalog()->availableProducts();
    }

    private function catalog(): CatalogInterface
    {
        return app(CatalogInterface::class);
    }

    #[Computed]
    public function totalAmount(): float
    {
        return collect($this->items)
            ->sum(fn(array $item): float => (float)$item['price'] * (int)$item['quantity']);
    }

    public function addProduct(int $productId): void
    {
        $product = $this->products->firstWhere('id', $productId);

        if (!$product) {
            $this->addError('items', 'Selected product is no longer available.');

            return;
        }

        if ((int)$product->stock_quantity <= 0) {
            $this->addError('items', 'Selected product is out of stock.');

            return;
        }
        if (array_key_exists($product->id, $this->items)) {
            $this->items[$product->id]['quantity'] = min(
                $this->items[$product->id]['quantity'] + 1,
                (int)$product->stock_quantity,
            );

            return;
        }

        $this->items[$product->id] = [
            'id' => $product->id,
            'categoryName' => $product->category?->name,
            'name' => $product->name,
            'description' => (string)($product->description ?? ''),
            'price' => (float)$product->price,
            'availableQuantity' => (int)$product->stock_quantity,
            'quantity' => 1,
        ];
    }

    public function removeProduct(int $productId): void
    {
        unset($this->items[$productId]);
    }

    public function updatedItems(): void
    {
        foreach ($this->items as $productId => $item) {
            $quantity = max(1, $item['quantity']);
            $availableQuantity = max(1, $item['availableQuantity']);

            $this->items[$productId]['quantity'] = min($quantity, $availableQuantity);
        }
    }

    /**
     * @return OrderedProduct[]
     */
    private function orderedProducts(): array
    {
        return collect($this->items)
            ->map(fn(array $item): OrderedProduct => new OrderedProduct(
                id: (int)$item['id'],
                categoryName: $item['categoryName'],
                name: $item['name'],
                description: $item['description'],
                price: (float)$item['price'],
                quantity: (int)$item['quantity'],
            ))
            ->values()
            ->all();
    }

    public function createOrder(): void
    {
        $this->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['nullable', 'string', 'max:255'],
            'customerAddress' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->catalog()->reduceStockOrFail($this->orderedProducts());
        } catch (\RuntimeException $exception) {
            $this->refreshSelectedProductSnapshots();
            $this->addError('items', $exception->getMessage());

            return;
        }

        DB::transaction(function (): void {
            $orderId = DB::table('orders')->insertGetId([
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'status' => OrderStatus::Pending->value,
                'total_amount' => $this->totalAmount(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($this->items as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => (float)$item['price'] * (int)$item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->reset([
            'customerName',
            'customerEmail',
            'customerPhone',
            'customerAddress',
            'items',
        ]);

        session()->flash('status', 'Order has been created successfully.');
    }

    private function refreshSelectedProductSnapshots(): void
    {
        $snapshots = $this->catalog()
            ->availableProducts($this->orderedProducts())
            ->keyBy('id');

        foreach ($this->items as $productId => $item) {
            $snapshot = $snapshots->get($productId);

            if (!$snapshot) {
                unset($this->items[$productId]);

                continue;
            }

            $this->items[$productId]['name'] = $snapshot->name;
            $this->items[$productId]['categoryName'] = $snapshot->category?->name;
            $this->items[$productId]['description'] = (string)($snapshot->description ?? '');
            $this->items[$productId]['price'] = (float)$snapshot->price;
            $this->items[$productId]['availableQuantity'] = (int)$snapshot->stock_quantity;
            $this->items[$productId]['quantity'] = min(
                (int)$item['quantity'],
                max(1, (int)$snapshot->stock_quantity),
            );
        }
    }
};
?>

<div>
    <div class="min-h-screen bg-gray-950 px-6 py-10 text-gray-100">
        <div class="mx-auto max-w-6xl">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-400">Order</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Create order</h1>
                    <p class="mt-2 text-sm text-gray-400">
                        Select products from the Catalog module and create a new order with editable quantities.
                    </p>
                </div>
            </div>

            @if (session('status'))
                <div
                    class="mb-6 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm font-medium text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_420px]">
                <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                    <div class="border-b border-gray-800 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Available products</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800 text-sm">
                            <thead
                                class="bg-gray-900/80 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Name</th>
                                <th scope="col" class="px-6 py-4">Category</th>
                                <th scope="col" class="px-6 py-4">Price</th>
                                <th scope="col" class="px-6 py-4">Stock</th>
                                <th scope="col" class="px-6 py-4"></th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-800">
                            @forelse ($this->products as $product)
                                <tr class="transition hover:bg-gray-800/40">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $product->name }}</div>
                                        @if ($product->description)
                                            <div class="mt-1 max-w-55 truncate text-xs text-gray-400">
                                                {{ $product->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-gray-300">
                                        {{ $product->categoryName ?? 'Uncategorized' }}
                                    </td>

                                    <td class="px-6 py-4 font-medium text-gray-100">
                                        €{{ number_format((float) $product->price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-300">
                                        {{ $product->stock_quantity }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <button
                                            type="button"
                                            wire:click="addProduct({{ $product->id }})"
                                            class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-gray-950 transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
                                            @disabled($product->stock_quantity <= 0)
                                        >
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                                        No products available yet.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                <form wire:submit="createOrder" class="space-y-6">
                    <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                        <div class="border-b border-gray-800 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">Customer info</h2>
                        </div>

                        <div class="space-y-4 px-6 py-5">
                            <div>
                                <label for="customer-name" class="text-sm font-medium text-gray-300">Name</label>
                                <input
                                    id="customer-name"
                                    type="text"
                                    wire:model="customerName"
                                    class="mt-2 w-full rounded-lg border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                >
                                @error('customerName')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer-email" class="text-sm font-medium text-gray-300">Email</label>
                                <input
                                    id="customer-email"
                                    type="email"
                                    wire:model="customerEmail"
                                    class="mt-2 w-full rounded-lg border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                >
                                @error('customerEmail')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer-phone" class="text-sm font-medium text-gray-300">Phone</label>
                                <input
                                    id="customer-phone"
                                    type="text"
                                    wire:model="customerPhone"
                                    class="mt-2 w-full rounded-lg border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                >
                            </div>

                            <div>
                                <label for="customer-address" class="text-sm font-medium text-gray-300">Address</label>
                                <textarea
                                    id="customer-address"
                                    wire:model="customerAddress"
                                    rows="3"
                                    class="mt-2 w-full rounded-lg border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
                        <div class="border-b border-gray-800 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">Order items</h2>
                        </div>

                        <div class="divide-y divide-gray-800">
                            @forelse ($items as $productId => $item)
                                <div class="px-6 py-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-medium text-white">{{ $item['name'] }}</div>
                                            <div class="mt-1 text-xs text-gray-400">
                                                €{{ number_format((float) $item['price'], 2) }}
                                                · {{ $item['availableQuantity'] }} available
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeProduct({{ $productId }})"
                                            class="text-xs font-medium text-red-400 transition hover:text-red-300"
                                        >
                                            Remove
                                        </button>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <label for="quantity-{{ $productId }}"
                                               class="text-sm text-gray-300">Quantity</label>
                                        <input
                                            id="quantity-{{ $productId }}"
                                            type="number"
                                            min="1"
                                            max="{{ $item['availableQuantity'] }}"
                                            wire:model.live="items.{{ $productId }}.quantity"
                                            class="w-24 rounded-lg border border-gray-700 bg-gray-950 px-3 py-2 text-right text-sm text-white outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                        >
                                    </div>

                                    <div class="mt-3 text-right text-sm font-medium text-gray-100">
                                        €{{ number_format((float) $item['price'] * (int) $item['quantity'], 2) }}
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center text-sm text-gray-400">
                                    Add at least one product to create an order.
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-gray-800 px-6 py-4">
                            @error('items')
                            <p class="mb-3 text-sm text-red-400">{{ $message }}</p>
                            @enderror

                            <div class="mb-4 flex items-center justify-between text-base font-semibold text-white">
                                <span>Total</span>
                                <span>USD{{ number_format($this->totalAmount(), 2) }}</span>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-lg bg-amber-500 px-4 py-3 text-sm font-semibold text-gray-950 transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
                                @disabled(empty($items))
                            >
                                Create order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
