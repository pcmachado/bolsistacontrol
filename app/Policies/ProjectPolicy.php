<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->isInstitutionScoped()) {
            return $user->activeInstitutionIds()->contains($project->institution_id);
        }

        if ($user->isUnitScoped()) {
            return $project->units()
                ->whereIn('units.id', $user->visibleUnitIds())
                ->exists();
        }

        return $user->visibleProjectIds()->contains($project->id);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    public function deleteProject(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']) && $this->view($user, $project);
    }
}
