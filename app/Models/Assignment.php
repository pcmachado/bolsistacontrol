<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scholarship_holder_id',
        'position_project_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function positionProject(): BelongsTo
    {
        return $this->belongsTo(PositionProject::class, 'position_project_id');
    }
}
