<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AttendanceRecord;

class AttendanceRecordPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        
    }

    /**
     * O método `before` é executado antes de qualquer outro método na policy.
     * Se ele retornar `true`, a ação é autorizada imediatamente.
     * Aqui, estamos a dar permissão total a administradores e coordenadores gerais.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole(['admin', 'coordenador_geral', 'coordenador_adjunto', 'bolsista'])) {
            return true;
        }

        return null; // Se não for admin, continua para a verificação específica do método.
    }

    /**
     * Determina se o utilizador pode atualizar o registo de frequência.
     * Condição: O registo deve estar no estado 'rascunho' E o utilizador deve ser o bolsista dono do registo.
     */
    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        // só o dono pode editar, e apenas se ainda for editável
        return $attendanceRecord->scholarship_holder_id === $user->scholarshipHolder->id
            && $attendanceRecord->isEditable();
    }

    /**
     * Bolsista pode excluir se for dono e registro ainda não enviado.
     */
    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->scholarshipHolder
            && $user->scholarshipHolder->id === $attendanceRecord->scholarship_holder_id
            && $attendanceRecord->status === 'draft';
    }

    /**
     * Determina se o utilizador pode submeter o registo para aprovação.
     * Condição: O registo deve estar no estado 'rascunho' E o utilizador deve ser o dono.
     */
    public function submit(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->scholarshipHolder
            && $user->scholarshipHolder->id === $attendanceRecord->scholarship_holder_id
            && $attendanceRecord->status === 'draft';
    }

    /**
     * Determina se o usuário pode homologar (aprovar) um registro.
     */
    public function approve(User $user, AttendanceRecord $record): bool
    {
        // Admin e coordenador geral podem homologar qualquer registro
        if ($user->hasRole(['admin', 'coordenador_geral'])) {
            return true;
        }

        // Coordenador adjunto pode homologar registros de bolsistas da sua unidade
        if ($user->hasRole('coordenador_adjunto')) {
            $unitIds = $user->units()->pluck('units.id');
            return $record->scholarshipHolder
                && $unitIds->contains($record->scholarshipHolder->unit_id);
        }

        // Bolsista comum não pode homologar
        return false;
    }

    public function reject(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $this->approve($user, $record);
    }


    public function isEditable(): bool
    {
        if ($this->status === 'draft') {
            return true;
        }

        if ($this->status === 'rejected' && $this->rejected_at) {
            return now()->diffInDays($this->rejected_at) <= 7;
        }

        return false;
    }
}
