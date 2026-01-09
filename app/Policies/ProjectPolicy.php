<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Ver se o usuário pode visualizar um projeto.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin pode tudo
        if ($user->isAdmin()) {
            return true;
        }

        // Usuário só pode ver se o projeto pertence à instituição ativa
        return $project->institution_id === $user->institution_id;
    }

    /**
     * Ver se o usuário pode atualizar um projeto.
     */
    public function update(User $user, Project $project): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $project->institution_id === $user->institution_id;
    }

    /**
     * Ver se o usuário pode deletar um projeto.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}
