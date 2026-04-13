<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class InstitutionScope implements Scope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($user->hasRole('admin')) {
            return;
        }

        if (! $user->institution_id) {
            $builder->whereRaw('1 = 0');
            return;
        }

        // Se o model tem institution_id diretamente
        if ($model->getConnection()
            ->getSchemaBuilder()
            ->hasColumn($model->getTable(), 'institution_id')
        ) {
            $builder->where(
                $model->getTable().'.institution_id',
                $user->institution_id
            );
            return;
        }

        // Se possui relacionamento unit()
        if (method_exists($model, 'unit')) {
            $builder->whereHas('unit', function ($q) use ($user) {
                $q->where('institution_id', $user->institution_id);
            });
        }
    }
}
