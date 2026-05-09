<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Define a hierarquia de poder das roles.
     * Quanto maior o número, mais poder a role tem.
     */
    protected array $roleHierarchy = [
        'superadmin' => 100,
        'admin' => 90,
        'coordenador_geral' => 70,
        'coordenador_adjunto_geral' => 60,
        'coordenador_adjunto' => 30,
        'bolsista' => 10,
        // Roles não listadas assumem valor 0
    ];

    /**
     * Retorna o peso da role.
     */
    private function getRoleWeight(string $roleName): int
    {
        return $this->roleHierarchy[$roleName] ?? 0;
    }

    /**
     * Retorna o maior peso entre as roles de um usuário.
     */
    private function getUserWeight(User $user): int
    {
        // Pega todas as roles do usuário e encontra o maior peso
        return $user->roles->map(fn ($role) => $this->getRoleWeight($role->name))->max() ?? 0;
    }

    /**
     * Verifica se o usuário tem nível hierárquico suficiente sobre o alvo.
     */
    private function canManageRole(User $user, Role $targetRole): bool
    {
        $userWeight = $this->getUserWeight($user);
        $targetWeight = $this->getRoleWeight($targetRole->name);

        // Regra de Ouro:
        // 1. Usuário deve ter peso maior que o alvo.
        // 2. Ou seja, Coordenador Geral (50) NÃO pode editar outro Geral (50),
        //    mas pode editar Adjunto (30).
        return $userWeight > $targetWeight;
    }

    // --- MÉTODOS DA POLICY ---

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.view')) {
            return false;
        }

        // Admin vê tudo
        if ($this->getUserWeight($user) >= 90) {
            return true;
        }

        // demais só veem inferiores
        return $this->getUserWeight($user) >= $this->getRoleWeight($role->name);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        // 1. Verifica permissão básica no banco
        if (! $user->hasPermissionTo('roles.edit')) {
            return false;
        }

        // 2. Proteção Hardcode para Superadmin (ninguém edita, só via banco/tinker se necessário)
        if ($role->name === 'superadmin') {
            return false;
        }

        // 3. Aplica a hierarquia
        return $this->canManageRole($user, $role);
    }

    public function delete(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.delete')) {
            return false;
        }

        if (in_array($role->name, ['admin', 'superadmin'])) {
            return false;
        }

        return $this->canManageRole($user, $role);
    }
}
