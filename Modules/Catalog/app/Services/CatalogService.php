<?php

namespace Modules\Catalog\Services;

use App\Contracts\CatalogInterface;
use App\Data\OrderedProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Models\Product;
use RuntimeException;
use Throwable;

class CatalogService implements CatalogInterface
{
    public function handle() {}

    /**
     * @return Collection<int, OrderedProduct>
     */
    public function availableProducts(?array $orderedProducts = null): Collection
    {
        $query = Product::query()
            ->with('category')
            ->orderBy('products.name');

        $productIds = collect($orderedProducts)
            ->map(fn (OrderedProduct $product) => $product->id)
            ->all();

        if (! empty($productIds)) {
            $query->whereIn('products.id', $productIds);
        }

        return $query
            ->get()
            ->map(fn (Product $product): OrderedProduct => new OrderedProduct(
                id: $product->id,
                categoryName: $product->category()->value('name'),
                name: $product->name,
                description: (string) ($product->description ?? ''),
                price: (float) $product->price,
                quantity: (int) $product->stock_quantity,
            ));
    }

    /**
     * @throws Throwable
     */
    public function reduceStockOrFail(array $orderedProducts): void
    {
        DB::transaction(function () use ($orderedProducts) {
            foreach ($orderedProducts as $orderedProduct) {
                /**
                 * @var OrderedProduct $orderedProduct
                 */
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($orderedProduct->id);

                if ($product === null) {
                    throw new RuntimeException("Product {$orderedProduct->id} not found.");
                }

                if ($product->stock_quantity < $orderedProduct->quantity) {
                    throw new RuntimeException("Insufficient stock for product {$product->name}.");
                }

                $product->decrement(
                    'stock_quantity',
                    $orderedProduct->quantity
                );
            }
        });
    }
}
