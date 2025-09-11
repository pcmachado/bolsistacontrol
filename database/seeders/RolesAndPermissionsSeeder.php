<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        Permission::create(['name' => 'admin_dashboard']);
        Permission::create(['name' => 'manage_attendances']);
        Permission::create(['name' => 'manage_reports']);
        Permission::create(['name' => 'scholarship_holder_dashboard']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_scholarship_holders']);
        Permission::create(['name' => 'manage_units']);
        Permission::create(['name' => 'manage_positions']);

        // Criar papéis e atribuir permissões
        $coordenadorGeralRole = Role::create(['name' => 'coordenador_geral']);
        $coordenadorGeralRole->givePermissionTo('admin_dashboard');
        $coordenadorGeralRole->givePermissionTo('manage_attendances');
        $coordenadorGeralRole->givePermissionTo('manage_reports');
        $coordenadorGeralRole->givePermissionTo('scholarship_holder_dashboard');
        $coordenadorGeralRole->givePermissionTo('manage_users');
        $coordenadorGeralRole->givePermissionTo('manage_scholarship_holders');
        $coordenadorGeralRole->givePermissionTo('manage_units');
        $coordenadorGeralRole->givePermissionTo('manage_positions');

        $coordenadorAdjuntoRole = Role::create(['name' => 'coordenador_adjunto']);
        $coordenadorAdjuntoRole->givePermissionTo('admin_dashboard');
        $coordenadorAdjuntoRole->givePermissionTo('manage_attendances');
        $coordenadorAdjuntoRole->givePermissionTo('manage_reports');
        $coordenadorAdjuntoRole->givePermissionTo('scholarship_holder_dashboard');


        $bolsistaRole = Role::create(['name' => 'bolsista']);
        $bolsistaRole->givePermissionTo('scholarship_holder_dashboard');


        // Exemplo de atribuição de papel a um usuário (em outro seeder ou no Tinker)
        // $user = \App\Models\User::find(1);
        // $user->assignRole('bolsista');
    }
}