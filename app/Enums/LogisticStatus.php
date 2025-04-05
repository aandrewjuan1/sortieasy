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
            self::Pending => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::Shipped => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::Delivered => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        };
    }
}
