<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Institution;

trait BelongsToInstitution
{
    protected static function bootBelongsToInstitution()
    {
        static::addGlobalScope('institution', function (Builder $builder) {
            if (session()->has('institution_id')) {
                $builder->where('institution_id', session('institution_id'));
            }
        });

        static::creating(function ($model) {
            if (session()->has('institution_id') && empty($model->institution_id)) {
                $model->institution_id = session('institution_id');
            }
        });
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
