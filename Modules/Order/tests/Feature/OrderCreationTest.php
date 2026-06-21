<?php

use App\Contracts\CatalogInterface;
use App\Data\OrderedProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductCategory;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('order creation page is accessible', function (): void {
    $response = $this->get('/orders/create');

    $response->assertSuccessful();
});

test('order can be created with products', function (): void {
    $category = ProductCategory::factory()->create([
        'name' => 'Electronics',
    ]);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Keyboard',
        'price' => 49.99,
        'stock_quantity' => 10,
    ]);

    $order = Order::create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+43123456789',
        'customer_address' => 'Vienna',
        'status' => 'pending',
        'total_amount' => 99.98,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'unit_price' => $product->price,
        'quantity' => 2,
        'total_price' => 99.98,
    ]);

    expect($order->items)
        ->toHaveCount(1);

    expect($item->product_name)
        ->toBe('Wireless Keyboard');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'customer_name' => 'John Doe',
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Wireless Keyboard',
        'quantity' => 2,
    ]);
});

test('product snapshot is stored in order item', function (): void {
    $category = ProductCategory::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Gaming Mouse',
        'price' => 25.50,
    ]);

    $order = Order::factory()->create();

    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'unit_price' => $product->price,
    ]);

    $product->update([
        'name' => 'Changed Product Name',
        'price' => 999.99,
    ]);

    $item->refresh();

    expect($item->product_name)
        ->toBe('Gaming Mouse');

    expect((float) $item->unit_price)
        ->toBe(25.50);
});

test('order details page is accessible', function (): void {
    $order = Order::factory()->create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'status' => OrderStatus::Pending,
        'total_amount' => 49.99,
    ]);

    $response = $this->get(route('order.show', $order->id));

    $response->assertSuccessful();
    $response->assertSee('Order details');
    $response->assertSee('John Doe');
});

test('pending order can be confirmed and product stock is reduced', function (): void {
    $category = ProductCategory::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Keyboard',
        'price' => 49.99,
        'stock_quantity' => 10,
    ]);

    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
        'total_amount' => 99.98,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'unit_price' => $product->price,
        'quantity' => 2,
        'total_price' => 99.98,
    ]);

    app(CatalogInterface::class)
        ->reduceStockOrFail([
            new OrderedProduct(
                id: $product->id,
                categoryName: null,
                name: $product->name,
                description: '',
                price: (float) $product->price,
                quantity: 2,
            ),
        ]);

    $order->update([
        'status' => OrderStatus::Confirmed,
    ]);

    $product->refresh();

    $order->refresh();

    expect($order->status)
        ->toBe(OrderStatus::Confirmed)
        ->and($product->stock_quantity)
        ->toBe(8);
});
