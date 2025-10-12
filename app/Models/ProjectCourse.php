<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCourse extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_courses';

    protected $fillable = [
        'course_id',
        'project_id',
        'semester',
        'year',
        'active',
        'start_date',
        'end_date',
        'capacity',
        'status'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
