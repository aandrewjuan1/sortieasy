<?php

namespace App\Enums;

enum AlertType: string
{
    case LowStock = 'low_stock';
    case OverStock = 'over_stock';
    case RestockSuggestion = 'restock_suggestion';
}
