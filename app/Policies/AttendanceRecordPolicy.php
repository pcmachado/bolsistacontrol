<?php

namespace App\Policies;

use App\Models\User;

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
        if ($user->hasRole(['admin', 'coordenador_geral'])) {
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
        return $attendanceRecord->status === AttendanceRecord::STATUS_DRAFT
            && $user->scholarshipHolder?->id === $attendanceRecord->scholarship_holder_id;
    }

    /**
     * Determina se o utilizador pode submeter o registo para aprovação.
     * Condição: O registo deve estar no estado 'rascunho' E o utilizador deve ser o dono.
     */
    public function submit(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $attendanceRecord->status === AttendanceRecord::STATUS_DRAFT
            && $user->scholarshipHolder?->id === $attendanceRecord->scholarship_holder_id;
    }

    /**
     * Determina se o utilizador pode aprovar um registo de frequência.
     * Condição: O utilizador deve ser 'coordenador_adjunto' de pelo menos uma das unidades do bolsista
     * E o registo deve estar no estado 'pendente'.
     */
    public function approve(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if (!$user->hasRole('coordenador_adjunto') || $attendanceRecord->status !== AttendanceRecord::STATUS_PENDING) {
            return false;
        }

        // Pega os IDs das unidades do coordenador.
        $coordinatorUnitIds = $user->units->pluck('id');

        // Pega os IDs das unidades do bolsista que fez o registo.
        $scholarshipHolderUnitIds = $attendanceRecord->scholarshipHolder->units->pluck('id');

        // Verifica se o coordenador e o bolsista partilham pelo menos uma unidade.
        return $coordinatorUnitIds->intersect($scholarshipHolderUnitIds)->isNotEmpty();
    }

    /**
     * Determina se o utilizador pode rejeitar um registo de frequência.
     * A lógica é a mesma da aprovação.
     */
    public function reject(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $this->approve($user, $attendanceRecord);
    }
}
