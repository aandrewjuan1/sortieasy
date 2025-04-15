<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use App\Enums\UserRole;

class SalePolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user)
    {
        return $user->role === UserRole::Admin;
    }

    public function edit(User $user, Sale $sale)
    {
        return $sale->user_id === null || $user->id === $sale->user_id || $user->role === UserRole::Admin;
    }
}
