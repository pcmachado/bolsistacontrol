<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AttendanceVisibilityService
{
    public function apply(Builder $query, User $user): Builder
    {
        // Admin / coordenação → vê tudo
        if ($user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ])) {
            return $query;
        }

        // Bolsista → só os próprios
        if ($user->scholarshipHolder) {
            return $query->where(
                'scholarship_holder_id',
                $user->scholarshipHolder->id
            );
        }

        // fallback defensivo
        return $query->whereRaw('1 = 0');
    }
}

