<?php

namespace App\Policies;

use App\Models\AttendanceSubmission;
use App\Models\User;

class AttendanceSubmissionPolicy
{
    /**
     * Visualizar a submissão
     */
    public function view(User $user, AttendanceSubmission $submission): bool
    {
        // Bolsista dono
        if ($user->scholarshipHolder?->id === $submission->scholarship_holder_id) {
            return true;
        }

        // Coordenação
        return $this->isReviewer($user);
    }

    /**
     * Enviar para homologação
     */
    public function submit(User $user, AttendanceSubmission $submission): bool
    {
        return $user->scholarshipHolder?->id === $submission->scholarship_holder_id
            && $submission->status === AttendanceSubmission::STATUS_DRAFT;
    }

    /**
     * Aprovar submissão
     */
    public function approve(User $user, AttendanceSubmission $submission): bool
    {
        return $submission->status === AttendanceSubmission::STATUS_SUBMITTED
        && $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ]);
    }

    /**
     * Rejeitar submissão
     */
    public function reject(User $user, AttendanceSubmission $submission): bool
    {
        return $this->approve($user, $submission);
    }

    /**
     * Define quem pode homologar
     */
    protected function isReviewer(User $user): bool
    {
        return $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ]);
    }
}
