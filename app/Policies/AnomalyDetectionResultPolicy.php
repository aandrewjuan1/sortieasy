<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;

class AnomalyDetectionResultPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function detectAnomaly(User $user)
    {
        return $user->role === UserRole::Admin;
    }
}
