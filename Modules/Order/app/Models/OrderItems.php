<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Order\Database\Factories\OrderItemsFactory;

class OrderItems extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OrderItemsFactory
    // {
    //     // return OrderItemsFactory::new();
    // }
}
