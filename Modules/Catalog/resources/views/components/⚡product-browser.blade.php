<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Catalog\Models\Product;

new class extends Component {
    use WithPagination;

    public int $perPage = 10;

    /**
     * @return LengthAwarePaginator
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->orderBy('name')
            ->paginate($this->perPage);
    }
};
?>

<div class="min-h-screen bg-gray-950 px-6 py-10 text-gray-100">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-gray-400">Catalog</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Products</h1>
                <p class="mt-2 text-sm text-gray-400">
                    Browse available products from the catalog module.
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
            <div class="border-b border-gray-800 px-6 py-4">
                <h2 class="text-base font-semibold text-white">Product list</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/80 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-4">Name</th>
                        <th scope="col" class="px-6 py-4">Category</th>
                        <th scope="col" class="px-6 py-4">Price</th>
                        <th scope="col" class="px-6 py-4">Stock</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-800">
                    @forelse ($this->products as $product)
                        <tr class="transition hover:bg-gray-800/40">
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $product->name }}</div>
                                @if ($product->description)
                                    <div class="mt-1 max-w-xl truncate text-xs text-gray-400">
                                        {{ $product->description }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-300">
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-100">
                                €{{ number_format((float) $product->price, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full border border-gray-700 px-2.5 py-1 text-xs font-medium text-gray-300">
                                        {{ $product->stock_quantity }} available
                                    </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                No products available yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-800 px-6 py-4">
                {{ $this->products->links() }}
            </div>
        </div>
    </div>
</div>
