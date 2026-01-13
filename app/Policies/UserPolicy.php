<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $logged)
    {
        if ($logged->hasRole('coordenador_adjunto')) {
            return true; // mas restrito no query do DataTable
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, user $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $logged, User $target)
    {
        if ($target->hasRole('superadmin')) {
            return $logged->id === $target->id; 
        }

        if ($logged->hasRole('superadmin')) {
            return true;
        }

        // 🔒 ninguém altera ADMIN exceto ele mesmo
        if ($target->hasRole('admin')) {
            return $logged->id === $target->id; 
        }

        // admin pode tudo
        if ($logged->hasRole('admin')) {
            return true;
        }

        // coordenador geral pode alterar adjunto + demais
        if ($logged->hasRole('coordenador_geral')) {
            return ! $target->hasRole('coordenador_geral')
                && ! $target->hasRole('admin')
                && ! $target->hasRole('superadmin');
        }

        // adjunto só altera usuários comuns
        if ($logged->hasRole('coordenador_adjunto')) {
            return ! $target->hasRole('coordenador_adjunto_geral')
                && ! $target->hasRole('coordenador_adjunto')
                && ! $target->hasRole('coordenador_geral')
                && ! $target->hasRole('admin')
                && ! $target->hasRole('superadmin');
        }

        // demais usuários não podem alterar ninguém
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $logged, User $target)
    {
        return $this->update($logged, $target);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, user $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, user $model): bool
    {
        return false;
    }
}
