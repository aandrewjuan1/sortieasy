<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine if the user can add a product.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine if the user can delete a product.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return bool
     */
    public function delete(User $user, Product $product)
    {
        return $user->role === UserRole::Admin;
    }
    public function edit(User $user, Product $product)
    {
        return $user->role === UserRole::Admin;
    }
}
