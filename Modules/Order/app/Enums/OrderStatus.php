<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Confirmed => 'warning',
            self::Shipped => 'info',
            self::Delivered => 'success',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-800 ring-gray-500/10',
            self::Confirmed => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
            self::Shipped => 'bg-blue-100 text-blue-800 ring-blue-700/10',
            self::Delivered => 'bg-green-100 text-green-800 ring-green-600/20',
        };
    }
}
