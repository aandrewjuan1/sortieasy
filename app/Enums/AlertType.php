<?php

namespace App\Enums;

enum AlertType: string
{
    case LowStock = 'low_stock';
    case OverStock = 'over_stock';
    case RestockSuggestion = 'restock_suggestion';

    public function label(): string
    {
        return match($this) {
            self::LowStock => 'Low Stock',
            self::OverStock => 'Over Stock',
            self::RestockSuggestion => 'Restock Suggestion',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LowStock => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::OverStock => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::RestockSuggestion => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        };
    }
}
