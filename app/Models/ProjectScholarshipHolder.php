<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectScholarshipHolder extends Pivot
{

    use HasFactory, SoftDeletes;
    
    protected $table = 'project_scholarship_holder';

    protected $fillable = [
        'project_id',
        'scholarship_holder_id',
        'position_id',
        'weekly_workload',
        'edital_portaria',
        'start_date',
        'end_date',
        'assignments',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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