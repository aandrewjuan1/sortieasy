<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case Normal = 'normal';
    case SlowMoving = 'slow_moving';
    case Obsolete = 'obsolete';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
