<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Institution;

trait BelongsToInstitution
{
    protected static function bootBelongsToInstitution()
    {
        static::addGlobalScope('institution', function (Builder $builder) {
            // só aplica o filtro se não for admin
            if (session()->has('institution_id') && !auth()->user()?->isAdmin()) {
                $builder->where('institution_id', session('institution_id'));
            }
        });
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
