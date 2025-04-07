<?php

namespace App\Enums;

enum SaleChannel: string
{
    case Online = 'online';
    case InStore = 'in_store';
    case Phone = 'phone';

    // Custom method to get all the values
    public static function getValues(): array
    {
        return array_map(fn ($enum) => $enum->value, self::cases());
    }

    // Method to return the label for human-friendly display
    public function label(): string
    {
        return match($this) {
            self::Online => 'Online',
            self::InStore => 'In-Store',
            self::Phone => 'Phone Order',
        };
    }
}
