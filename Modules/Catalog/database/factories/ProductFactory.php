<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductCategory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'category_id' => ProductCategory::query()->inRandomOrder()->value('id'),
            'price' => $this->faker->randomFloat(2, 5, 1000),
            'description' => $this->faker->sentence,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
