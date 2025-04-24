<?php

namespace App\Enums;

enum AnomalyStatus: string
{
    case Normal = 'normal';
    case Anomalous = 'anomalous';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
