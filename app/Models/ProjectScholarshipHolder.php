<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectScholarshipHolder extends Pivot
{

    use HasFactory, SoftDeletes;
    
    protected $table = 'project_scholarship_holders';

    protected $fillable = [
        'project_id',
        'scholarship_holder_id',
        'position_id',
        'monthly_workload',
        'start_date',
        'end_date',
        'status'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    } 

}