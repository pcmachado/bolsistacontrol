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
    public function apply(
        Builder $query,
        User $user,
        string $scope = 'my'
    ): Builder {

        // ========================
        // ADMIN
        // ========================
        if ($user->hasRole('admin')) {
            return match ($scope) {
                'my'  => $query->whereRaw('1 = 0'),
                default => $query, // vê tudo
            };
        }

        // ========================
        // USUÁRIO PRECISA SER BOLSISTA
        // ========================
        if (! $user->scholarshipHolder) {
            return $query->whereRaw('1 = 0');
        }

        // ========================
        // CONTEXTOS
        // ========================
        return match ($scope) {

            // 🔹 Meus pagamentos
            'my' => $query->where('scholarship_holder_id', $user->scholarshipHolder->id),

            // 🔹 Unidade
            'unit' => $query->where('unit_id', $user->unit_id),

            // 🔹 Instituição
            'institution' => $query->whereHas('unit', fn ($q) =>
                $q->where('institution_id', $user->institution_id)
            ),

            // 🔹 Fallback seguro
            default => $query->whereRaw('1 = 0'),
        };
    }
}
