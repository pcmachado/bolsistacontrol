<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectPosition extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_positions';

    protected $fillable = [
        'project_id',
        'position_id',
        'assignments',
        'hourly_rate',
        'weekly_hour_limit'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

}
