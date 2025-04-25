<?php

namespace App\Enums;

enum LogisticStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'bg-yellow-300 text-yellow-800',
            self::Shipped => 'bg-blue-300 text-blue-800',
            self::Delivered => 'bg-green-300 text-green-800',
        };
    }
    public function colorHover(): string
    {
        return match($this) {
            self::Pending => 'bg-yellow-200 text-yellow-800',
            self::Shipped => 'bg-blue-200 text-blue-800',
            self::Delivered => 'bg-green-200 text-green-800',
        };
    }
}
