<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;

class DemandForecastPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function runForecasts(User $user)
    {
        return $user->role === UserRole::Admin;
    }
}
