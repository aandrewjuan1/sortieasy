<?php

namespace App\Enums;

enum TransactionType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Return = 'return';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match($this) {
            self::Purchase => 'Purchase',
            self::Sale => 'Sale',
            self::Return => 'Return',
            self::Adjustment => 'Adjustment',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Purchase => 'bg-green-100 text-green-800',
            self::Sale => 'bg-blue-100 text-blue-800',
            self::Return => 'bg-yellow-100 text-yellow-800',
            self::Adjustment => 'bg-purple-100 text-purple-800',
        };
    }
}
