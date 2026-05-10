<?php

namespace Tests\Feature;

use App\Support\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_registry_contains_expected_permissions_and_role_templates(): void
    {
        $permissions = PermissionRegistry::flattenPermissionNames();
        $templates = PermissionRegistry::roleTemplates();

        $this->assertContains('roles.view', $permissions);
        $this->assertContains('dashboard.admin.view', $permissions);
        $this->assertArrayHasKey('coordenador_geral', $templates);
        $this->assertEquals(80, $templates['coordenador_geral']['level']);
        $this->assertCount(count($permissions), PermissionRegistry::resolveRolePermissions('admin'));
    }

    public function test_roles_and_permissions_seeder_creates_roles_with_levels_and_permissions(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder'])->assertExitCode(0);

        $role = Role::where('name', 'coordenador_geral')->first();

        $this->assertNotNull($role);
        $this->assertSame(80, $role->level);
        $this->assertTrue($role->hasPermissionTo('roles.view'));
        $this->assertTrue($role->hasPermissionTo('attendance.homologate'));
    }
}
