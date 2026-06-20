<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Enums\OrderStatus;

// use Modules\Order\Database\Factories\OrderFactory;

/**
 * @property int $id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $customer_address
 * @property OrderStatus $status
 */
class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}
