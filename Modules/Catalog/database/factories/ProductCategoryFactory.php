<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = $this->faker->words(1, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
