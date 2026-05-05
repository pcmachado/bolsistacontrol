<?php

namespace App\Policies;

use App\Models\NotificationSetting;
use App\Models\User;

class NotificationSettingPolicy
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
    public function view(User $user, NotificationSetting $notificationSetting): bool
    {
        // Super admin sempre pode
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Acesso administrativo (admin + coordenação)
        if ($user->canAccessAdministrative()) {
            // Configuração global (sem instituição)
            if (! $notificationSetting->institution_id) {
                return true;
            }

            // Pertence à instituição do usuário
            return $user->accessibleInstitutionIds()
                ->contains($notificationSetting->institution_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canAccessAdministrative();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationSetting $notificationSetting): bool
    {
        // Super admin sempre pode
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Acesso administrativo (admin + coordenação)
        if ($user->canAccessAdministrative()) {
            // Configuração global (sem instituição)
            if (! $notificationSetting->institution_id) {
                return true;
            }

            // Pertence à instituição do usuário
            return $user->accessibleInstitutionIds()
                ->contains($notificationSetting->institution_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NotificationSetting $notificationSetting): bool
    {
        // Super admin sempre pode
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Acesso administrativo (admin + coordenação)
        if ($user->canAccessAdministrative()) {
            // Configuração global (sem instituição)
            if (! $notificationSetting->institution_id) {
                return true;
            }

            // Pertence à instituição do usuário
            return $user->accessibleInstitutionIds()
                ->contains($notificationSetting->institution_id);
        }

        return false;
    }
}
