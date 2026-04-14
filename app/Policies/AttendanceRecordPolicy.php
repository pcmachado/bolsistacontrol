<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
{
    /**
     * Visualizar registro
     */
    public function view(User $user, AttendanceRecord $record): bool
    {
        // Bolsista vê o próprio
        if ($user->scholarshipHolder?->id === $record->scholarship_holder_id) {
            return true;
        }

        // Coordenação / admin vê todos
        return $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ]);
    }

    /**
     * Editar registro
     */
    public function update(User $user, AttendanceRecord $record): bool
    {
        // dono
        if ($user->scholarshipHolder?->id !== $record->scholarship_holder_id) {
            return false;
        }

        // nunca enviado → pode editar
        if ($record->attendance_submission_id === null) {
            return true;
        }

        // 🔥 verificar status da submissão
        $submission = $record->submission;

        if (!$submission) {
            return false;
        }

        return $submission->status === \App\Models\AttendanceSubmission::STATUS_REJECTED;
    }

    /**
     * Excluir registro
     */
    public function delete(User $user, AttendanceRecord $record): bool
    {
        // Mesma regra da edição
        return $this->update($user, $record);
    }
}
