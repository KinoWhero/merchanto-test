<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
