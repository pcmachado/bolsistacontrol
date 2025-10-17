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
    public function update(User $user, AttendanceRecord $record): bool
    {
        return $user->scholarshipHolder
            && $user->scholarshipHolder->id === $record->scholarship_holder_id
            && in_array($record->status, ['draft','rejected']);
    }

    /**
     * Bolsista pode excluir se for dono e registro ainda não enviado.
     */
    public function delete(User $user, AttendanceRecord $record): bool
    {
        return $user->scholarshipHolder
            && $user->scholarshipHolder->id === $record->scholarship_holder_id
            && $record->status === 'draft';
    }

    /**
     * Determina se o utilizador pode submeter o registo para aprovação.
     * Condição: O registo deve estar no estado 'rascunho' E o utilizador deve ser o dono.
     */
    public function submit(User $user, AttendanceRecord $record): bool
    {
        return $user->scholarshipHolder
            && $user->scholarshipHolder->id === $record->scholarship_holder_id
            && $record->status === 'draft';
    }

    /**
     * Determina se o utilizador pode aprovar um registo de frequência.
     * Condição: O utilizador deve ser 'coordenador_adjunto' de pelo menos uma das unidades do bolsista
     * E o registo deve estar no estado 'pendente'.
     */
    public function approve(User $user, AttendanceRecord $record): bool
    {
        return $user->hasRole(['admin','coordenador_geral','coordenador_adjunto'])
            && $record->status === 'submitted';
    }

    /**
     * Determina se o utilizador pode rejeitar um registo de frequência.
     * A lógica é a mesma da aprovação.
     */
    public function reject(User $user, AttendanceRecord $record): bool
    {
        return $user->hasRole(['admin','coordenador_geral','coordenador_adjunto'])
            && $record->status === 'submitted';
    }
}
