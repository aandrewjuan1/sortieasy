<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;
use App\Enums\UserRole;

class TransactionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user)
    {
        return $user->role === UserRole::Admin;
    }

    public function edit(User $user, Transaction $transaction)
    {
        return $transaction->created_by === null || $user->id === $transaction->created_by || $user->role === UserRole::Admin;
    }
}
