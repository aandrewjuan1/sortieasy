<?php

namespace App\Enums;

enum AuditAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';

    public function label(): string
    {
        return match($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::Deleted => 'Deleted',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Created => 'bg-green-100 text-green-800',
            self::Updated => 'bg-yellow-100 text-yellow-800',
            self::Deleted => 'bg-red-100 text-red-800',
        };
    }

    public function colorHover(): string
    {
        return match($this) {
            self::Created => 'bg-green-200 text-green-800',
            self::Updated => 'bg-yellow-200 text-yellow-800',
            self::Deleted => 'bg-red-200 text-red-800',
        };
    }
}
