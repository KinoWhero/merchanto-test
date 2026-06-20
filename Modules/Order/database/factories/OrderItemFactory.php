<?php

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id'),
            'product_id' => $this->faker->words(3, true),
            'product_name' => $this->faker->sentence(),
            'unit_price' => $this->faker->randomFloat(2, 5, 100),
            'total_price' => $this->faker->randomFloat(2, 5, 1000),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
