<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\Database\Factories\OrderFactory;
use Modules\Order\Enums\OrderStatus;

/**
 * @property int $id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $customer_address
 * @property OrderStatus $status
 * @property float $total_amount
 */
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
    ];

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this
            ->hasMany(OrderItem::class)
            ->orderBy('product_name');
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
