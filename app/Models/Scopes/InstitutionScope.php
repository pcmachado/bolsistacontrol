<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        // Sem usuário autenticado
        if (! $user) {
            return;
        }

        // Superadmin vê tudo
        if ($user->hasRole('superadmin')) {
            return;
        }

        // Usuário sem instituição → não vê nada
        if (! $user->institution_id) {
            $builder->whereRaw('1 = 0');
            return;
        }

        if (in_array('institution_id', $model->getFillable())) {
            $builder->where('institution_id', $user->institution_id);
            return;
        }

        /*
         |---------------------------------------
         | Modelos que possuem unit_id direto
         |---------------------------------------
         */
        if (in_array('unit_id', $model->getFillable())) {
            $builder->whereHas('unit', fn ($q) =>
                $q->where('institution_id', $user->institution_id)
            );
            return;
        }

        /*
         |---------------------------------------
         | Modelos que passam por scholarshipHolder
         |---------------------------------------
         */
        if (method_exists($model, 'scholarshipHolder')) {
            $builder->whereHas('scholarshipHolder.unit', fn ($q) =>
                $q->where('institution_id', $user->institution_id)
            );
        }
    }
}
