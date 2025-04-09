<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Supplier;
use Illuminate\Auth\Access\Response;

class SupplierPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function delete(User $user)
    {
        return $user->role === UserRole::Admin;
    }
    public function create(User $user)
    {
        return $user->role === UserRole::Admin;
    }
    public function edit(User $user)
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Supplier $supplier): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return false;
    }
}
