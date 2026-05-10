<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    private function getUserLevel(User $user): int
    {
        return $user->roles->max('level') ?? 0;
    }

    private function getRoleLevel(Role $role): int
    {
        return $role->level ?? 0;
    }

    private function canManageRole(User $user, Role $role): bool
    {
        return $this->getUserLevel($user) > $this->getRoleLevel($role);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.view')) {
            return false;
        }

        return $this->getUserLevel($user) >= $this->getRoleLevel($role);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.edit')) {
            return false;
        }

        return $this->canManageRole($user, $role);
    }

    public function delete(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.delete')) {
            return false;
        }

        return $this->canManageRole($user, $role);
    }
}
