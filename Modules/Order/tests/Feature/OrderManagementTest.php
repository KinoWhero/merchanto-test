<?php

use App\Contracts\CatalogInterface;
use App\Data\OrderedProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductCategory;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('admin can access order management page', function (): void {
    $admin = User::factory()->create([
        'email' => 'admin@mail.com',
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get('/admin/orders');

    $response->assertSuccessful();
});

test('order status can be updated', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    $order->update([
        'status' => OrderStatus::Confirmed,
    ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => OrderStatus::Confirmed->value,
    ]);
});

test('order contains items', function (): void {
    $order = Order::factory()->create();

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_name' => 'Wireless Keyboard',
        'quantity' => 2,
        'unit_price' => 49.99,
        'total_price' => 99.98,
    ]);

    expect($order->items)
        ->toHaveCount(1);

    expect($order->items->first()->product_name)
        ->toBe('Wireless Keyboard');
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
        'stock_quantity' => 10,
    ]);

    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'unit_price' => $product->price,
        'quantity' => 2,
        'total_price' => $product->price * 2,
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

    expect($order->fresh()->status)
        ->toBe(OrderStatus::Confirmed)
        ->and($product->stock_quantity)
        ->toBe(8);

});
