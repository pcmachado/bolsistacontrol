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

        // Admin
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasAnyRole(['coordenador_geral', 'coordenador_adjunto_geral', 'financeiro'])) {
            return $payment->unit_id === $user->unit_id;
        }

        return $payment->scholarship_holder_id === $user->scholarshipHolder?->id;
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
        if (!$user->hasAnyRole(['admin', 'financeiro', 'coordenador_geral', 'coordenador_adjunto_geral'])) {
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
        return $user->scholarshipHolder
            && $payment->scholarship_holder_id === $user->scholarshipHolder->id
            && $payment->isPaid();
    }

    /**
     * Cancelar ou reverter pagamento (futuro)
     */
    public function cancel(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral', 'financeiro']);
    }
}
