<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_name
 * @property string $description
 * @property float $unit_price
 * @property float $total_price
 * @property int $quantity
 */
class OrderItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'description',
        'unit_price',
        'total_price',
        'quantity',
    ];
}
