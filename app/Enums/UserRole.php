<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Employee = 'employee';

    public function displayName(): string
    {
        return match($this) {
            self::Admin => 'Admin',
            self::Employee => 'Employee',
        };
    }
}
