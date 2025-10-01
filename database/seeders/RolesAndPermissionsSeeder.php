<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permissões
        $permissions = [
            'admin_dashboard',
            'manage_attendances',
            'manage_reports',
            'scholarship_holder_dashboard',
            'manage_users',
            'manage_scholarship_holders',
            'manage_units',
            'manage_positions',
            'manage_projects',
            'manage_instituitions',
        ];

        // Criar permissões se não existirem
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criação de Permissões
        Permission::firstOrCreate(['name' => ' view_users']);
        
        Permission::firstOrCreate(['name' => 'manage_units']);
        Permission::firstOrCreate(['name' => 'manage_positions']);
        Permission::firstOrCreate(['name' => 'manage_scholarship_holders']);
        Permission::firstOrCreate(['name' => 'manage_projects']);
        Permission::firstOrCreate(['name' => 'manage_attendances']);
        Permission::firstOrCreate(['name' => 'manage_reports']);
        Permission::firstOrCreate(['name' => 'admin_dashboard']);
        Permission::firstOrCreate(['name' => 'scholarship_holder_dashboard']);
        Permission::firstOrCreate(['name' => 'manage_instituitions']);
        Permission::firstOrCreate(['name' => 'manage_users']);


        // Criar papéis
        $coordenadorGeralRole = Role::firstOrCreate(['name' => 'coordenador_geral', 'guard_name' => 'web']);
        $coordenadorAdjuntoRole = Role::firstOrCreate(['name' => 'coordenador_adjunto', 'guard_name' => 'web']);
        $bolsistaRole          = Role::firstOrCreate(['name' => 'bolsista', 'guard_name' => 'web']);
        $adminRole        = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Atribuir permissões
        $coordenadorGeralRole->syncPermissions([
            'admin_dashboard',
            'manage_attendances',
            'manage_reports',
            'scholarship_holder_dashboard',
            'manage_users',
            'manage_scholarship_holders',
            'manage_units',
            'manage_positions',
            'manage_projects',
        ]);

        $coordenadorAdjuntoRole->syncPermissions([
            'admin_dashboard',
            'manage_attendances',
            'manage_reports',
            'scholarship_holder_dashboard',
        ]);

        $bolsistaRole->syncPermissions([
            'scholarship_holder_dashboard',
            'manage_attendances',
        ]);

        $adminRole->syncPermissions(Permission::all());
        // Exemplo de atribuição de papel a um usuário específico (opcional)
        // $user = \App\Models\User::find(1);
        // $user->assignRole('bolsista');
    }
}