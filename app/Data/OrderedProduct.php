<?php

namespace App\Data;

readonly class OrderedProduct
{
    public function __construct(
        public int $id,
        public ?string $categoryName,
        public string $name,
        public string $description,
        public float $price,
        public int $quantity,
    ) {}
}
