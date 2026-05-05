<?php

namespace App\Policies;

use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplatePolicy
{
    public function update(User $user, EmailTemplate $emailTemplate): bool
    {
        // 🔴 superadmin sempre pode
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // 🔵 acesso administrativo (admin + coordenação)
        if ($user->canAccessAdministrative()) {

            // template global (sem instituição)
            if (! $emailTemplate->institution_id) {
                return true;
            }

            // pertence à instituição do usuário
            return $user->accessibleInstitutionIds()
                ->contains($emailTemplate->institution_id);
        }

        return false;
    }
}
