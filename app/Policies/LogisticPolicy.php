<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Logistic;
use App\Enums\LogisticStatus;

class LogisticPolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user)
    {
        return $user->role === UserRole::Admin;
    }

    public function edit(User $user, Logistic $logistic)
    {
        return $user->role === UserRole::Admin;
    }

    public function editStatus(User $user, Logistic $logistic): bool
    {
        return $logistic->status !== LogisticStatus::Delivered;
    }
}
