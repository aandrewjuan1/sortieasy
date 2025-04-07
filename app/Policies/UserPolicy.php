<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the admin dashboard.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}
