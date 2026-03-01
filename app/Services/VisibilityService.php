<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class VisibilityService
{
    public function apply(
        Builder $query,
        User $user,
        string $context = 'admin' // self | admin
    ): Builder {

        $model = $query->getModel();

        /*
        |==================================================
        | CONTEXTO SELF (MINHA ÁREA)
        |==================================================
        */
        if ($context === 'self') {

            if ($user->scholarshipHolder) {

                // Model tem scholarship_holder_id direto
                if ($this->hasColumn($model, 'scholarship_holder_id')) {
                    return $query->where(
                        'scholarship_holder_id',
                        $user->scholarshipHolder->id
                    );
                }

                // Model passa por scholarshipHolder
                if (method_exists($model, 'scholarshipHolder')) {
                    return $query->whereHas('scholarshipHolder', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            }

            return $query->whereRaw('1 = 0');
        }

        /*
        |==================================================
        | CONTEXTO ADMINISTRATIVO
        |==================================================
        */

        // ADMIN vê tudo
        if ($user->hasRole('admin')) {
            return $query;
        }

        // ------------------------------------------------
        // MODELS COM scholarshipHolder RELAÇÃO
        // ------------------------------------------------
        if (method_exists($model, 'scholarshipHolder')) {

            // Coordenação Geral → toda instituição
            if ($user->hasRole(['coordenador_geral','coordenador_adjunto_geral'])) {
                return $query->whereHas('scholarshipHolder.unit', function ($q) use ($user) {
                    $q->where('institution_id', $user->institution_id);
                });
            }

            // Coordenador Adjunto → apenas suas unidades
            if ($user->hasRole('coordenador_adjunto')) {
                return $query->whereHas('scholarshipHolder', function ($q) use ($user) {
                    $q->whereIn('unit_id', $user->units->pluck('id'));
                });
            }

            // Bolsista acessando admin → apenas próprio
            if ($user->scholarshipHolder) {
                return $query->where(
                    'scholarship_holder_id',
                    $user->scholarshipHolder->id
                );
            }
        }

        // ------------------------------------------------
        // MODELS COM unit_id DIRETO
        // ------------------------------------------------
        if ($this->hasColumn($model, 'unit_id')) {

            if ($user->hasRole(['coordenador_geral','coordenador_adjunto_geral'])) {
                return $query->whereHas('unit', fn($q) =>
                    $q->where('institution_id', $user->institution_id)
                );
            }

            if ($user->hasRole('coordenador_adjunto')) {
                return $query->whereIn(
                    'unit_id',
                    $user->units->pluck('id')
                );
            }
        }

        return $query->whereRaw('1 = 0');
    }

    protected function hasColumn($model, string $column): bool
    {
        return Schema::hasColumn($model->getTable(), $column);
    }
}