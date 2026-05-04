<?php

namespace App\Models\Scopes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user || $user->hasRole('superadmin')) {
            return;
        }

        $institutionIds = $user->activeInstitutionIds();
        $unitIds = $user->visibleUnitIds();

        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'institution_id')) {
            if ($institutionIds->isEmpty()) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->whereIn($model->getTable().'.institution_id', $institutionIds);

            return;
        }

        if (! method_exists($model, 'unit')) {
            return;
        }

        if ($unitIds->isNotEmpty()) {
            $builder->whereHas('unit', function ($query) use ($unitIds) {
                $query->whereIn('units.id', $unitIds);
            });

            return;
        }

        if ($institutionIds->isEmpty()) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->whereHas('unit', function ($query) use ($institutionIds) {
            $query->whereIn('institution_id', $institutionIds);
        });
    }

    public function handle(Request $request, Closure $next)
    {
        if ($institutionId = session('admin_institution_context') ?? session('institution_id')) {
            app()->instance('institution_id', $institutionId);
        }

        return $next($request);
    }
}
