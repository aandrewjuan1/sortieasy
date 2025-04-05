<?php

namespace App\Enums;

enum Severity: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    // Get all enum values as an array
    public static function getValues(): array
    {
        return array_map(fn($severity) => $severity->value, self::cases());
    }
}
