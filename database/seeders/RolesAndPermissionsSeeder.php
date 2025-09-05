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

        // Criar permissões
        Permission::create(['name' => 'acessar dashboard coordenador']);
        Permission::create(['name' => 'homologar frequencia']);
        Permission::create(['name' => 'gerar relatorios']);

        // Criar papéis e atribuir permissões
        $coordenadorGeralRole = Role::create(['name' => 'coordenador_geral']);
        $coordenadorGeralRole->givePermissionTo('acessar dashboard coordenador');
        $coordenadorGeralRole->givePermissionTo('gerar relatorios');

        $coordenadorAdjuntoRole = Role::create(['name' => 'coordenador_adjunto']);
        $coordenadorAdjuntoRole->givePermissionTo('acessar dashboard coordenador');
        $coordenadorAdjuntoRole->givePermissionTo('homologar frequencia');

        $bolsistaRole = Role::create(['name' => 'bolsista']);

        // Exemplo de atribuição de papel a um usuário (em outro seeder ou no Tinker)
        // $user = \App\Models\User::find(1);
        // $user->assignRole('bolsista');
    }
}