<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseScholarshipHolder extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_scholarship_holders';

    protected $fillable = [
        'course_id',
        'scholarship_holder_id',
        'enrollment_date',
        'completion_date',
        'status'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }
}
