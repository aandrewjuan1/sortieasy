<?php

namespace App\Policies;

use App\Models\User;
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
    public function viewAdminDashboard(User $user)
    {
        return $user->role === 'admin';
    }
}
