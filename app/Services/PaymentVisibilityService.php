<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Eloquent\Builder;

class PaymentVisibilityService
{
    /**
     * Aplica escopo de visibilidade de pagamentos.
     */
    public function apply(Builder $query, User $user): Builder
    {
        // Admin NÃO entra em "meus pagamentos"
        if ($user->hasRole('admin')) {
            return $query->whereRaw('1 = 0');
        }

        // Usuário precisa ser pagável
        if (! $user->scholarshipHolder) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHasMorph(
            'payable',
            [ScholarshipHolder::class],
            fn ($q) => $q->where('id', $user->scholarshipHolder->id)
        );
    }
}
