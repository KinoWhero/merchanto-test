<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Books',
            'Home',
            'Office',
            'Other',
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate([
                'name' => $category,
                'slug' => str($category)->slug(),
            ]);
        }
    }
}
