<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductCategory;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('admin can access product management page', function (): void {
    $admin = User::factory()->create([
        'email' => 'admin@mail.com',
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get('/admin/products');

    $response->assertSuccessful();
});

test('product can be created with category', function (): void {
    $category = ProductCategory::factory()->create([
        'name' => 'Electronics',
    ]);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Keyboard',
        'description' => 'Compact wireless keyboard for everyday work.',
        'price' => 49.99,
        'stock_quantity' => 25,
    ]);

    expect($product->category)
        ->toBeInstanceOf(ProductCategory::class)
        ->and($product->category->name)
        ->toBe('Electronics');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'category_id' => $category->id,
        'name' => 'Wireless Keyboard',
        'description' => 'Compact wireless keyboard for everyday work.',
        'price' => '49.99',
        'stock_quantity' => 25,
    ]);
});

test('product can be updated', function (): void {
    $product = Product::factory()->create([
        'name' => 'Old Product Name',
        'price' => 10.00,
        'stock_quantity' => 5,
    ]);

    $product->update([
        'name' => 'Updated Product Name',
        'price' => 25.50,
        'stock_quantity' => 12,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product Name',
        'price' => '25.50',
        'stock_quantity' => 12,
    ]);
});
