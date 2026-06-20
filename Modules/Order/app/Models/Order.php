<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Enums\OrderStatus;

// use Modules\Order\Database\Factories\OrderFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}
