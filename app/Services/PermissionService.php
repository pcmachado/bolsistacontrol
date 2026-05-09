<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * Mapa de categorias de permissões.
     * Agrupa permissões relacionadas por categoria.
     */
    private array $categoryMap = [
        'dashboard' => 'Dashboards',
        'users' => 'Usuários',
        'roles' => 'Funções (Roles)',
        'permissions' => 'Permissões',
        'institutions' => 'Instituições',
        'units' => 'Unidades',
        'scholarship_holders' => 'Bolsistas',
        'positions' => 'Cargos/Posições',
        'projects' => 'Projetos',
        'attendance' => 'Frequência',
        'payment' => 'Pagamentos',
        'financial' => 'Financeiro',
        'course' => 'Cursos',
        'discipline' => 'Disciplinas',
        'classoffering' => 'Ofertas de Aulas',
        'classsession' => 'Sessões de Aulas',
        'reports' => 'Relatórios',
        'notifications' => 'Notificações',
        'profile' => 'Perfil',
        'document_template' => 'Templates de Documentos',
        'supervisor_assignment' => 'Atribuição de Supervisor',
        'intelligent_alert_setting' => 'Configurações de Alertas',
        'funding_source' => 'Fontes de Financiamento',
    ];

    /**
     * Retorna todas as permissões organizadas por categoria.
     */
    public function getPermissionsByCategory(): Collection
    {
        $permissions = Permission::all();
        $categorized = collect();

        foreach ($this->categoryMap as $prefix => $categoryName) {
            $categoryPermissions = $permissions->filter(fn ($p) => str_starts_with($p->name, $prefix))
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'label' => $this->humanizePermissionName($p->name),
                ])
                ->values();

            if ($categoryPermissions->isNotEmpty()) {
                $categorized[$categoryName] = $categoryPermissions;
            }
        }

        return $categorized;
    }

    /**
     * Retorna permissões de um role organizadas por categoria.
     */
    public function getRolePermissionsByCategory(Role $role): Collection
    {
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $categorized = collect();

        foreach ($this->categoryMap as $prefix => $categoryName) {
            $categoryPermissions = Permission::where('name', 'like', "{$prefix}%")
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'label' => $this->humanizePermissionName($p->name),
                    'assigned' => in_array($p->id, $rolePermissions),
                ])
                ->values();

            if ($categoryPermissions->isNotEmpty()) {
                $categorized[$categoryName] = $categoryPermissions;
            }
        }

        return $categorized;
    }

    /**
     * Converte um nome de permissão para formato legível.
     * Ex: 'attendance.view.own' -> 'Visualizar - Próprio'
     */
    public function humanizePermissionName(string $permissionName): string
    {
        $parts = explode('.', $permissionName);

        // Remove o primeiro elemento (categoria) pois já é a categoria
        array_shift($parts);

        // Traduz termos comuns
        $translations = [
            'view' => 'Visualizar',
            'create' => 'Criar',
            'edit' => 'Editar',
            'update' => 'Atualizar',
            'delete' => 'Deletar',
            'manage' => 'Gerenciar',
            'own' => 'Próprio',
            'unit' => 'Unidade',
            'institution' => 'Instituição',
            'approve' => 'Aprovar',
            'reject' => 'Rejeitar',
            'homologate' => 'Homologar',
            'submit' => 'Enviar',
            'confirm' => 'Confirmar',
            'download' => 'Baixar',
            'receipt' => 'Recibo',
            'closure' => 'Fechamento',
            'permissions' => 'Permissões',
            'individual' => 'Individual',
        ];

        $translated = array_map(fn ($part) => $translations[$part] ?? ucfirst($part), $parts);

        return implode(' - ', $translated);
    }

    /**
     * Retorna a descrição de uma categoria.
     */
    public function getCategoryDescription(string $category): string
    {
        $descriptions = [
            'Dashboards' => 'Acesso a diferentes painéis e visualizações',
            'Usuários' => 'Gerenciamento completo de usuários',
            'Funções (Roles)' => 'Gerenciamento de roles/funções do sistema',
            'Permissões' => 'Gerenciamento de permissões do sistema',
            'Instituições' => 'Gerenciamento de instituições',
            'Unidades' => 'Gerenciamento de unidades organizacionais',
            'Bolsistas' => 'Gerenciamento de bolsistas',
            'Cargos/Posições' => 'Gerenciamento de posições e cargos',
            'Projetos' => 'Gerenciamento de projetos',
            'Frequência' => 'Controle e homologação de frequência',
            'Pagamentos' => 'Processamento e gerenciamento de pagamentos',
            'Financeiro' => 'Gestão financeira e fechamento',
            'Cursos' => 'Gerenciamento de cursos',
            'Disciplinas' => 'Gerenciamento de disciplinas',
            'Ofertas de Aulas' => 'Gerenciamento de ofertas de aulas',
            'Sessões de Aulas' => 'Gerenciamento de sessões de aulas',
            'Relatórios' => 'Geração e gerenciamento de relatórios',
            'Notificações' => 'Gerenciamento de notificações',
            'Perfil' => 'Gerenciamento do perfil pessoal',
            'Templates de Documentos' => 'Gerenciamento de templates de documentos',
            'Atribuição de Supervisor' => 'Gerenciamento de atribuição de supervisores',
            'Configurações de Alertas' => 'Gerenciamento de configurações de alertas',
            'Fontes de Financiamento' => 'Gerenciamento de fontes de financiamento',
        ];

        return $descriptions[$category] ?? '';
    }

    /**
     * Sincroniza permissões de um role com base em um array de IDs.
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        // Limpar cache de permissões após sincronização
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Retorna o total de permissões de um role.
     */
    public function getRolePermissionCount(Role $role): int
    {
        return $role->permissions()->count();
    }

    /**
     * Retorna o percentual de permissões de um role em relação ao total.
     */
    public function getRolePermissionPercentage(Role $role): float
    {
        $totalPermissions = Permission::count();
        $rolePermissions = $this->getRolePermissionCount($role);

        return $totalPermissions > 0 ? round(($rolePermissions / $totalPermissions) * 100, 2) : 0;
    }
}
