<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Pode visualizar pagamentos?
     */
    public function view(User $user, Payment $payment): bool
    {
        // Admin e coordenador geral veem tudo
        if ($user->hasRole(['Admin', 'coordenador_geral'])) {
            return true;
        }

        // Coordenador adjunto só da própria unidade
        if ($user->hasRole('coordenador_adjunto')) {
            return $user->unit_id === $payment->unit_id;
        }

        // Bolsista só vê os seus
        if ($user->scholarshipHolder) {
            return $payment->scholarship_holder_id === $user->scholarshipHolder->id;
        }

        return false;
    }

    /**
     * Pode marcar pagamento como pago?
     */
    public function markAsPaid(User $user, Payment $payment): bool
    {
        if (! $payment->isSent()) {
            return false;
        }

        return $user->hasRole([
            'Admin',
            'coordenador_geral',
            'coordenador_adjunto',
            'financeiro'
        ]);
    }

    /**
     * Pode confirmar recebimento?
     */
    public function confirm(User $user, Payment $payment): bool
    {
        if (! $payment->isPaid()) {
            return false;
        }

        return $user->scholarshipHolder &&
               $payment->scholarship_holder_id === $user->scholarshipHolder->id;
    }
}
