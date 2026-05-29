<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class RoleAccess
{
    public static function assignableRoleNames(User $user): Collection
    {
        $templates = collect(PermissionRegistry::roleTemplates());

        if ($user->hasRole('superadmin')) {
            return $templates->keys()->values();
        }

        $maxLevel = self::highestRoleLevel($user);

        return $templates
            ->filter(fn (array $template) => ($template['level'] ?? 0) < $maxLevel)
            ->keys()
            ->values();
    }

    public static function canAssignRole(User $user, string $roleName): bool
    {
        return self::assignableRoleNames($user)->contains($roleName);
    }

    public static function highestRoleLevel(User $user): int
    {
        $templates = PermissionRegistry::roleTemplates();

        return $user->roles
            ->map(fn ($role) => $templates[$role->name]['level'] ?? 0)
            ->max() ?? 0;
    }
}
