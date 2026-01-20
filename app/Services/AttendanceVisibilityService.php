<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AttendanceVisibilityService
{
    /**
     * Aplica regras de visibilidade conforme usuário e modo da tela.
     */
    public function apply(Builder $query,User $user,string $mode): Builder {

        // ==========================
        // MODO: MINHAS FREQUÊNCIAS
        // ==========================
        if ($mode === 'my') {
            return $this->onlySelf($query, $user);
        }

        // ==========================
        // MODO: HOMOLOGAÇÃO
        // ==========================
        if ($mode === 'homologation') {
            return $this->forHomologation($query, $user);
        }

        // ==========================
        // MODO: ADMIN / PADRÃO
        // ==========================
        return $this->defaultScope($query, $user);
    }

    /**
     * Apenas os próprios registros
     */
    protected function onlySelf(Builder $query, User $user): Builder
    {
        if ($user->scholarshipHolder) {
            return $query->where(
                'scholarship_holder_id',
                $user->scholarshipHolder->id
            );
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Homologações pendentes
     */
    protected function forHomologation(Builder $query, User $user): Builder
    {
        $query->where('status', AttendanceRecord::STATUS_SUBMITTED);

        return $this->defaultScope($query, $user);
    }

    /**
     * Visão administrativa
     */
    protected function defaultScope(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return $query;
        }
        
        // coordenações gerais → instituição
        if ($user->hasRole([
            'coordenador_geral',
            'coordenador_adjunto_geral'
        ])) {
            return $query->whereHas('scholarshipHolder.unit', fn ($q) =>
                $q->where('institution_id', $user->institution_id)
            );
        }

        // Coordenador adjunto → unidade
        if ($user->hasRole('coordenador_adjunto')) {
            return $query->whereHas('scholarshipHolder', fn ($q) =>
                $q->where('unit_id', $user->unit_id)
            );
        }

        // Bolsista comum
        if ($user->scholarshipHolder !== null) {
            return $this->onlySelf($query, $user);
        }

        // sem permissão
        return $query->whereRaw('1 = 0');
    }
}
