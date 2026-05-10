<?php

namespace Database\Seeders;

use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = PermissionRegistry::flattenPermissionNames();

        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionRegistry::roleTemplates() as $roleName => $template) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web', 'level' => $template['level']]
            );

            $permissionSet = PermissionRegistry::resolveRolePermissions($roleName);
            $permissions = Permission::whereIn('name', $permissionSet)->get();
            $role->syncPermissions($permissions);
        }
    }
}
