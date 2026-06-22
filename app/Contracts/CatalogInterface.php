<?php

namespace App\Contracts;

use App\Data\OrderedProduct;
use Illuminate\Support\Collection;

interface CatalogInterface
{
    /**
     * @param  OrderedProduct[]|null  $orderedProducts
     * @return Collection<int, OrderedProduct>
     */
    public function availableProducts(?array $orderedProducts = null): Collection;

    /**
     * @param  OrderedProduct[]  $orderedProducts
     */
    public function reduceStockOrFail(array $orderedProducts): void;
}
