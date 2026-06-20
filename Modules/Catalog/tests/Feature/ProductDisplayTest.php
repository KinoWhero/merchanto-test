<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductCategory;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('catalog page is accessible', function (): void {
    $response = $this->get('/catalogs');

    $response->assertSuccessful();
});

test('products are displayed on catalog page', function (): void {
    $category = ProductCategory::factory()->create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Keyboard',
    ]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Gaming Mouse',
    ]);

    $response = $this->get('/catalogs');

    $response
        ->assertSuccessful()
        ->assertSee('Wireless Keyboard')
        ->assertSee('Gaming Mouse');
});

test('catalog page is paginated', function (): void {
    $category = ProductCategory::factory()->create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    Product::factory()
        ->count(15)
        ->create([
            'category_id' => $category->id,
        ]);

    $response = $this->get('/catalogs');

    $response
        ->assertSuccessful()
        ->assertSee('Showing')
        ->assertSee('of')
        ->assertSee('15')
        ->assertSee('results')
        ->assertSee('Go to page 2');
});
