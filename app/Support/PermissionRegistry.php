<?php

namespace App\Support;

class PermissionRegistry
{
    public static function permissions(): array
    {
        return [
            'Dashboards' => [
                'dashboard.admin.view' => 'Visualizar painel administrativo',
                'dashboard.scholarship_holder.view' => 'Visualizar painel de bolsista',
                'dashboard.financial.view' => 'Visualizar painel financeiro',
                'dashboard.global.view' => 'Visualizar painel global',
                'dashboard.teacher.view' => 'Visualizar painel de professor',
                'dashboard.unit.view' => 'Visualizar painel da unidade',
            ],
            'Usuários' => [
                'users.manage' => 'Gerenciar usuários',
            ],
            'Funções (Roles)' => [
                'roles.manage' => 'Gerenciar funções',
                'roles.create' => 'Criar funções',
                'roles.delete' => 'Excluir funções',
                'roles.view' => 'Visualizar funções',
                'roles.edit' => 'Editar funções',
                'roles.update' => 'Atualizar funções',
                'roles.permissions.update' => 'Atualizar permissões de função',
                'roles.permissions.manage' => 'Gerenciar permissões de função',
            ],
            'Permissões' => [
                'permissions.manage' => 'Gerenciar permissões',
            ],
            'Instituições' => [
                'institutions.manage' => 'Gerenciar instituições',
            ],
            'Unidades' => [
                'units.manage' => 'Gerenciar unidades',
            ],
            'Bolsistas' => [
                'scholarship_holders.manage' => 'Gerenciar bolsistas',
            ],
            'Cargos/Posições' => [
                'positions.manage' => 'Gerenciar cargos e posições',
            ],
            'Projetos' => [
                'projects.manage' => 'Gerenciar projetos',
            ],
            'Frequência' => [
                'attendance.view.own' => 'Visualizar frequência própria',
                'attendance.create' => 'Criar frequência',
                'attendance.edit.own' => 'Editar frequência própria',
                'attendance.delete.own' => 'Excluir frequência própria',
                'attendance.submit' => 'Enviar frequência',
                'attendance.homologate' => 'Homologar frequência',
                'attendance.reject' => 'Rejeitar frequência',
                'attendance.view.unit' => 'Visualizar frequência da unidade',
                'attendance.view.institution' => 'Visualizar frequência da instituição',
            ],
            'Pagamentos' => [
                'payment.view.own' => 'Visualizar pagamento próprio',
                'payment.create' => 'Criar pagamento',
                'payment.manage' => 'Gerenciar pagamentos',
                'payment.approve' => 'Aprovar pagamento',
                'payment.reject' => 'Rejeitar pagamento',
                'payment.view.unit' => 'Visualizar pagamentos da unidade',
                'payment.view.institution' => 'Visualizar pagamentos da instituição',
                'payment.confirm.own' => 'Confirmar pagamento próprio',
                'payment.download.receipt' => 'Baixar recibo',
            ],
            'Financeiro' => [
                'financial.view' => 'Visualizar financeiro',
                'financial.manage' => 'Gerenciar financeiro',
                'financial.closure.manage' => 'Gerenciar fechamento financeiro',
                'funding_source.manage' => 'Gerenciar fontes de financiamento',
            ],
            'Relatórios' => [
                'reports.manage' => 'Gerenciar relatórios',
                'reports.view.own_individual' => 'Visualizar relatório individual',
            ],
            'Notificações' => [
                'notifications.manage' => 'Gerenciar notificações',
            ],
            'Ofertas de Aulas' => [
                'classoffering.view' => 'Visualizar ofertas de aulas',
                'classoffering.manage' => 'Gerenciar ofertas de aulas',
            ],
            'Sessões de Aulas' => [
                'classsession.view' => 'Visualizar sessões de aulas',
                'classsession.manage' => 'Gerenciar sessões de aulas',
            ],
            'Cursos' => [
                'course.manage' => 'Gerenciar cursos',
            ],
            'Disciplinas' => [
                'discipline.manage' => 'Gerenciar disciplinas',
            ],
            'Perfil' => [
                'profile.update.own' => 'Atualizar perfil próprio',
            ],
            'Templates de Documentos' => [
                'document_template.manage' => 'Gerenciar templates de documentos',
            ],
            'Atribuição de Supervisor' => [
                'supervisor_assignment.manage' => 'Gerenciar atribuição de supervisor',
            ],
            'Configurações de Alertas' => [
                'intelligent_alert_setting.manage' => 'Gerenciar configurações de alertas',
            ],
        ];
    }

    public static function allPermissionNames(): array
    {
        return array_merge(...array_values(self::permissions()));
    }

    public static function flattenPermissionNames(): array
    {
        return array_keys(array_merge(...array_values(self::permissions())));
    }

    public static function roleTemplates(): array
    {
        return [
            'superadmin' => [
                'label' => 'Superadmin',
                'level' => 100,
                'permissions' => '*',
            ],
            'admin' => [
                'label' => 'Admin',
                'level' => 90,
                'permissions' => '*',
            ],
            'coordenador_geral' => [
                'label' => 'Coordenador Geral',
                'level' => 80,
                'permissions' => [
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
                    'permissions.manage',
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
                ],
            ],
            'coordenador_adjunto_geral' => [
                'label' => 'Coordenador Adjunto Geral',
                'level' => 70,
                'permissions' => [
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
                ],
            ],
            'coordenador_adjunto' => [
                'label' => 'Coordenador Adjunto',
                'level' => 60,
                'permissions' => [
                    'dashboard.admin.view',
                    'dashboard.scholarship_holder.view',
                    'attendance.homologate',
                    'attendance.reject',
                    'attendance.view.unit',
                    'reports.manage',
                    'notifications.manage',
                ],
            ],
            'professor' => [
                'label' => 'Professor',
                'level' => 50,
                'permissions' => [
                    'dashboard.teacher.view',
                    'classoffering.view',
                    'classsession.view',
                    'course.manage',
                    'discipline.manage',
                    'notifications.manage',
                    'profile.update.own',
                ],
            ],
            'apoio_administrativo' => [
                'label' => 'Apoio Administrativo',
                'level' => 40,
                'permissions' => [
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
                ],
            ],
            'supervisor' => [
                'label' => 'Supervisor',
                'level' => 30,
                'permissions' => [
                    'dashboard.teacher.view',
                    'attendance.homologate',
                    'attendance.reject',
                    'attendance.view.unit',
                    'payment.approve',
                    'payment.reject',
                    'notifications.manage',
                    'profile.update.own',
                ],
            ],
            'orientador' => [
                'label' => 'Orientador',
                'level' => 20,
                'permissions' => [
                    'dashboard.teacher.view',
                    'attendance.homologate',
                    'attendance.reject',
                    'attendance.view.unit',
                    'notifications.manage',
                    'profile.update.own',
                ],
            ],
            'bolsista' => [
                'label' => 'Bolsista',
                'level' => 10,
                'permissions' => [
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
                ],
            ],
        ];
    }

    public static function resolveRolePermissions(string $roleName): array
    {
        $template = self::roleTemplates()[$roleName] ?? null;

        if ($template === null) {
            return [];
        }

        if ($template['permissions'] === '*') {
            return self::flattenPermissionNames();
        }

        return $template['permissions'];
    }
}
