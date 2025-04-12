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
        return array_map(fn($channel) => $channel->value, self::cases());
    }

    // Method to return the label for human-friendly display
    public static function getLabel(string $channel): string
    {
        return match($channel) {
            'online' => 'Online',
            'in_store' => 'In-Store',
            'phone' => 'Phone Order',
            default => 'Unknown',
        };
    }

    // Method to get the text color for each sale channel
    public static function getTextColor(string $channel): string
    {
        return match($channel) {
            'online' => 'blue',
            'in_store' => 'green',
            'phone' => 'purple',
            default => 'gray',
        };
    }

    public function label(): string
    {
        return match($this) {
            self::Online => 'Online',
            self::InStore => 'In-Store',
            self::Phone => 'Phone Order',
        };
    }

    // Method to get the background color for each sale channel
    public static function getBgColor(string $channel): string
    {
        return match ($channel) {
            'online' => 'bg-blue-100 text-blue-800 hover:bg-blue-200',
            'in_store' => 'bg-green-100 text-green-800 hover:bg-green-200',
            'phone' => 'bg-purple-100 text-purple-800 hover:bg-purple-200',
            default => 'bg-gray-100 text-gray-100 hover:bg-gray-200',
        };
    }
}
