<?php

namespace App\Services;

use App\Support\PermissionRegistry;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    public function getPermissionsByCategory(?Role $role = null): Collection
    {
        $selectedPermissionIds = $role ? $role->permissions->pluck('id')->toArray() : [];
        $permissionNames = PermissionRegistry::flattenPermissionNames();
        $existingPermissions = Permission::whereIn('name', $permissionNames)
            ->get()
            ->keyBy('name');

        return collect(PermissionRegistry::permissions())->mapWithKeys(function (array $permissions, string $category) use ($existingPermissions, $selectedPermissionIds) {
            $categoryPermissions = collect($permissions)
                ->map(function (string $label, string $name) use ($existingPermissions, $selectedPermissionIds) {
                    if (! $existingPermissions->has($name)) {
                        return null;
                    }

                    $permission = $existingPermissions->get($name);

                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'label' => $label,
                        'assigned' => in_array($permission->id, $selectedPermissionIds, true),
                    ];
                })
                ->filter()
                ->values();

            return $categoryPermissions->isNotEmpty() ? [$category => $categoryPermissions] : [];
        });
    }

    public function getRolePermissionsByCategory(Role $role): Collection
    {
        return $this->getPermissionsByCategory($role);
    }

    public function getAllPermissions(): Collection
    {
        return collect(PermissionRegistry::flattenPermissionNames());
    }

    public function getRolePermissionCount(Role $role): int
    {
        return $role->permissions()->count();
    }

    public function getRolePermissionPercentage(Role $role): float
    {
        $totalPermissions = Permission::count();
        $rolePermissions = $this->getRolePermissionCount($role);

        return $totalPermissions > 0 ? round(($rolePermissions / $totalPermissions) * 100, 2) : 0;
    }

    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function getRoleTemplates(): array
    {
        return PermissionRegistry::roleTemplates();
    }
}
