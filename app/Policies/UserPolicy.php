<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin', 'admin_dinas');
    }

    public function view(User $user, User $target): bool
    {
        return $user->is($target)
            || $user->hasRole('super_admin')
            || ($user->hasRole('admin_dinas') && ! $target->hasRole('super_admin'));
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin', 'admin_dinas');
    }

    public function update(User $user, User $target): bool
    {
        return $user->is($target)
            || $user->hasRole('super_admin')
            || ($user->hasRole('admin_dinas') && ! $target->hasRole('super_admin'));
    }

    public function manageAccount(User $user, User $target): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('admin_dinas') && ! $target->hasRole('super_admin'));
    }
}
