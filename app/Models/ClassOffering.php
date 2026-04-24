<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'class_offerings';

    protected $fillable = [
        'course_id',
        'project_id',
        'unit_id',
        'name',
        'semester',
        'year',
        'start_date',
        'end_date',
        'active',
        'capacity',
        'status'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'class_offering_disciplines')
            ->withPivot(['id', 'teacher_id', 'workload', 'schedule', 'room'])
            ->withTimestamps();
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'scholarship_holder_class_offering')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function studentRecords(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ClassOfferingSubmission::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class,'class_offering_student','class_offering_id','student_id');
    }

    public function classOfferingSubmissions(): HasMany
    {
        return $this->hasMany(ClassOfferingSubmission::class);
    }

    public function monthRecords(): HasMany
    {
        return $this->hasMany(StudentMonthRecord::class);
    }

}
