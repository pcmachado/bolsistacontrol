<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Lista Consolidada e Limpa de Permissões
        $permissions = [
            // Dashboards
            'dashboard.admin.view',
            'dashboard.scholarship_holder.view',
            'dashboard.financial.view',
            'dashboard.global.view',
            'dashboard.teacher.view',
            'dashboard.unit.view',
            
            // CRUD Gerais (Gerenciamento Completo)
            'users.manage', // CRUD de Usuários
            'permissions.manage', // CRUD de Permissões
            'roles.manage', // CRUD de Papéis (Roles)
            'institutions.manage', // CRUD de Instituições
            'scholarship_holders.manage', // CRUD de Bolsistas
            'units.manage', // CRUD de Unidades
            'positions.manage', // CRUD de Cargos
            'projects.manage', // CRUD de Projetos
            
            // Módulo de Frequência (Attendance)
            'attendance.view.own', // Ver suas próprias frequências
            'attendance.create',
            'attendance.edit.own',
            'attendance.delete.own',
            'attendance.submit', // CORRIGIDO
            'attendance.homologate', // Aprovar registros
            'attendance.reject', // CORRIGIDO
            'attendance.view.unit', // Ver frequências da unidade
            'attendance.view.institution', // Ver frequências da instituição
            
            // Módulo de Pagamentos (Payments)
            'payment.view.own',
            'payment.create', 
            'payment.manage', // CRUD de pagamentos (admin)
            'payment.approve',
            'payment.reject',
            'payment.view.unit',
            'payment.view.institution',
            'payment.confirm.own', // Bolsista confirmar recebimento
            'payment.download.receipt', // Bolsista baixar recibo
            
            // Módulo Financeiro
            'financial.view', // Visualização de dados financeiros
            'financial.manage', // Gestão financeira geral
            'financial.closure.manage', // Gerenciar fechamento financeiro
            'funding_source.manage', // Gerenciar fontes de financiamento
            
            // Módulo de Relatórios e Notificações
            'reports.manage', // Gerenciamento de relatórios administrativos
            'reports.view.own_individual', // Ver relatório individual (bolsista)
            'notifications.manage', // Gerenciar notificações
            
            // Módulo de Aulas (Class Offering)
            'classoffering.view',
            'classoffering.manage',
            'classsession.view',
            'classsession.manage',
            'course.manage', // CRUD de Cursos
            'discipline.manage', // CRUD de Disciplinas
            
            // Outros Módulos e Ações
            'profile.update.own', // Atualizar perfil próprio
            'document_template.manage', // Gerenciar templates de documentos
            'supervisor_assignment.manage', // Gerenciar atribuição de supervisor
            'intelligent_alert_setting.manage', // Gerenciar configurações de alertas

            'roles.manage',
            'roles.create',
            'roles.delete',
            'roles.view',
            'roles.edit',
            'roles.update',
            'roles.permissions.update', // Alterar permissões da função - corrigido de
            'roles.permissions.manage',
        ];

        // 3. Criar permissões (apenas uma vez e de forma limpa)
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 4. Criação de Papéis (Roles)
        $coordenadorGeralRole       = Role::firstOrCreate(['name' => 'coordenador_geral', 'guard_name' => 'web']);
        $coordenadorAdjuntoGeralRole= Role::firstOrCreate(['name' => 'coordenador_adjunto_geral', 'guard_name' => 'web']);
        $coordenadorAdjuntoRole     = Role::firstOrCreate(['name' => 'coordenador_adjunto', 'guard_name' => 'web']);
        $bolsistaRole               = Role::firstOrCreate(['name' => 'bolsista', 'guard_name' => 'web']);
        $supervisorRole             = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $apoioRole                  = Role::firstOrCreate(['name' => 'apoio_administrativo', 'guard_name' => 'web']);
        $orientadorRole             = Role::firstOrCreate(['name' => 'orientador', 'guard_name' => 'web']);
        $professorRole              = Role::firstOrCreate(['name' => 'professor', 'guard_name' => 'web']);
        $adminRole                  = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $superadminRole             = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        
        // Admin e Superadmin recebem todas as permissões
        $adminRole->syncPermissions(Permission::all());
        $superadminRole->syncPermissions(Permission::all());

        // 5. Atribuição de permissões por perfil (ajustada e expandida)
        
        // Coordenador Geral - Acesso total a Dashboards e Gestão
        $coordenadorGeralRole->syncPermissions([
            'dashboard.admin.view',
            'dashboard.scholarship_holder.view',
            'dashboard.financial.view',
            'dashboard.global.view',
            'dashboard.teacher.view',
            'dashboard.unit.view',
            'users.manage',
            'scholarship_holders.manage',
            'units.manage',
            'positions.manage',
            'projects.manage',
            'institutions.manage',
            'permissions.manage', // Permissão adicionada
            
            'attendance.homologate',
            'attendance.reject',
            'attendance.view.unit',
            'attendance.view.institution',

            'payment.manage',
            'payment.approve',
            'payment.reject',
            'payment.view.unit',
            'payment.view.institution',
            
            'financial.manage',
            'financial.closure.manage',
            'reports.manage',
            'notifications.manage',
            'document_template.manage',
            'supervisor_assignment.manage',
            'intelligent_alert_setting.manage',

            'roles.manage',
            'roles.create',
            'roles.delete',
            'roles.view',
            'roles.edit',
            'roles.update',
            'roles.permissions.update', 
            'roles.permissions.manage',
        ]);
        
        // Coordenador Adjunto Geral - Similar ao Coordenador Geral, mas pode ter menos acesso a permissões/financeiro.
        $coordenadorAdjuntoGeralRole->syncPermissions([
            'dashboard.admin.view',
            'dashboard.scholarship_holder.view',
            'dashboard.financial.view',
            'dashboard.global.view',
            'dashboard.unit.view',
            'scholarship_holders.manage',
            'units.manage',
            'positions.manage',
            'projects.manage',
            'institutions.manage',
            
            'attendance.homologate',
            'attendance.reject',
            'attendance.view.unit',
            'attendance.view.institution',

            'payment.manage',
            'payment.approve',
            'payment.reject',
            'payment.view.unit',
            'payment.view.institution',
            
            'reports.manage',

            'notifications.manage',
            'document_template.manage',
            'supervisor_assignment.manage',
            'intelligent_alert_setting.manage',

            'roles.manage',
            'roles.create',
            'roles.delete',
            'roles.view',
            'roles.edit',
            'roles.update',
            'roles.permissions.update',
            'roles.permissions.manage',
        ]);

        // Coordenador Adjunto - Acesso a dashboards e homologação
        $coordenadorAdjuntoRole->syncPermissions([
            'dashboard.admin.view',
            'dashboard.scholarship_holder.view',
            'attendance.homologate',
            'attendance.reject',
            'attendance.view.unit',
            'reports.manage',
            'notifications.manage',
        ]);
        
        // Bolsista - Ações limitadas ao próprio usuário
        $bolsistaRole->syncPermissions([
            'dashboard.scholarship_holder.view',
            'profile.update.own',
            'attendance.view.own',
            'attendance.create',
            'attendance.edit.own',
            'attendance.delete.own',
            'attendance.submit',
            'payment.view.own',
            'payment.confirm.own',
            'payment.download.receipt',
            'reports.view.own_individual',
            'notifications.manage',
        ]);
        
        // Supervisor/Orientador - Poderá visualizar e homologar frequências de bolsistas sob sua supervisão.
        // A lógica de "sob sua supervisão" é geralmente implementada via policies ou escopos de query, não apenas permissions.
        $supervisorRole->syncPermissions([
            'dashboard.teacher.view',
            'attendance.homologate',
            'attendance.reject',
            'attendance.view.unit', // Se o supervisor/orientador for de uma unidade
            'payment.approve',
            'payment.reject',
            'notifications.manage',
            'profile.update.own',
        ]);

        // Apoio Administrativo - Foco em gestão de dados e pagamento
        $apoioRole->syncPermissions([
            'dashboard.admin.view',
            'scholarship_holders.manage',
            'units.manage',
            'positions.manage',
            'institutions.manage',
            
            'attendance.view.unit',
            
            'payment.manage',
            'payment.view.unit',
            'financial.view',
            'financial.closure.manage',
            'notifications.manage',
            'document_template.manage',
        ]);

        // Professor - Acesso a dashboards de professor e cursos/disciplinas
        $professorRole->syncPermissions([
            'dashboard.teacher.view',
            'classoffering.view',
            'classsession.view',
            'course.manage',
            'discipline.manage',
            'notifications.manage',
            'profile.update.own',
        ]);
    }
}