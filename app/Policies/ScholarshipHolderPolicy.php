<?php

namespace App\Policies;

use App\Models\ScholarshipHolder;
use App\Models\User;

class ScholarshipHolderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAccessAdministrative();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(\App\Models\User $user, \App\Models\ScholarshipHolder $holder): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->canAccessAdministrative()) {
            return $user->activeInstitutionIds()
                ->contains($holder->unit?->institution_id);
        }

        return $user->id === $holder->user_id;
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
    public function update(User $user, ScholarshipHolder $scholarshipHolder): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        $holderInstitutionId = $scholarshipHolder->unit?->institution_id
            ?? $scholarshipHolder->user?->institution_id;

        if (! $holderInstitutionId || ! $user->activeInstitutionIds()->contains($holderInstitutionId)) {
            return false;
        }

        if ($scholarshipHolder->user) {
            return $user->can('update', $scholarshipHolder->user);
        }

        return $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScholarshipHolder $scholarshipHolder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ScholarshipHolder $scholarshipHolder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ScholarshipHolder $scholarshipHolder): bool
    {
        return false;
    }
}
