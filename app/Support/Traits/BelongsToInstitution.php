<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static void addGlobalScope(string $identifier, \Closure $scope)
 * @method static void creating(\Closure $callback)
 */
trait BelongsToInstitution
{
    /**
     * O Laravel chama automaticamente bootedNomeDoTrait()
     * para não conflitar com o booted() nativo do Model.
     */
    protected static function bootedBelongsToInstitution(): void
    {
        // Aplica o Global Scope para filtrar queries automaticamente
        static::addGlobalScope('institution', function (Builder $query) {
            // Usa o container (ou auth()->user()->institution_id, dependendo da sua regra)
            if (app()->has('institution_id') && $institutionId = app('institution_id')) {
                $query->where('institution_id', $institutionId);
            }
        });

        // Preenche automaticamente o institution_id ao criar um novo registro
        static::creating(function ($model) {
            if (app()->has('institution_id') && $institutionId = app('institution_id')) {
                $model->institution_id = $institutionId;
            }
        });
    }
}
