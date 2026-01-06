<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Models\FinancialClosure;

class PaymentPolicy
{
    /**
     * Visualizar listagem geral
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
            'financeiro',
        ]);
    }

    /**
     * Visualizar um pagamento específico
     */
    public function view(User $user, Payment $payment): bool
    {
        // Admin e coordenador geral veem tudo
        if ($user->hasAnyRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral', 'financeiro'])) {
            return true;
        }

        // Coordenador adjunto → apenas unidade
        if ($user->hasRole('coordenador_adjunto')) {
            return $user->units->contains($payment->unit_id);
        }

        // Bolsista → apenas o próprio
        return $user->scholarshipHolder?->id === $payment->scholarship_holder_id;
    }

    /**
     * Criar pagamento
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral', 'financeiro']);
    }

    /**
     * Marcar pagamento como pago
     */
    public function markAsPaid(User $user, Payment $payment): bool
    {
        if (!$user->hasAnyRole(['admin', 'financeiro'])) {
            return false;
        }

        if ($payment->status !== Payment::STATUS_SENT) {
            return false;
        }

        return !FinancialClosure::isClosed(
            $payment->unit_id,
            $payment->month,
            $payment->year
        );
    }

    /**
     * Confirmar pagamento (bolsista)
     */
    public function confirm(User $user, Payment $payment): bool
    {
        return
            $user->scholarshipHolder &&
            $payment->scholarship_holder_id === $user->scholarshipHolder->id &&
            $payment->status === Payment::STATUS_PAID;
    }

    /**
     * Cancelar ou reverter pagamento (futuro)
     */
    public function cancel(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral', 'financeiro']);
    }
}
