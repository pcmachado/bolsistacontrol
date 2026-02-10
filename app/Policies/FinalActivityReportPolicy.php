<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FinalActivityReport;

class FinalActivityReportPolicy
{
    public function view(User $user, FinalActivityReport $report): bool
    {
        return $user->id === $report->scholarshipHolder->user_id
            || $user->hasAnyRole([
                'admin',
                'coordenador_geral',
                'coordenador_adjunto_geral',
                'coordenador_adjunto',
            ]);
    }

    public function create(User $user): bool
    {
        return $user->scholarshipHolder !== null;
    }

    public function update(User $user, FinalActivityReport $report): bool
    {
        return $user->id === $report->scholarshipHolder->user_id
            && $report->status === FinalActivityReport::STATUS_DRAFT;
    }

    public function submit(User $user, FinalActivityReport $report): bool
    {
        return $this->update($user, $report);
    }

    public function approve(User $user, FinalActivityReport $report): bool
    {
        return $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
        ]) && $report->status === FinalActivityReport::STATUS_SUBMITTED;
    }
}
