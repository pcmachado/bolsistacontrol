<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

        public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'position_project')
                    ->withPivot(['assignments', 'hourly_rate', 'weekly_hour_limit'])
                    ->withTimestamps();
    }
}
