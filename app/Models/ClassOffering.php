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
        'hours_per_day',
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
            ->withPivot(['id', 'teacher_id', 'workload', 'planned_total_hours', 'schedule', 'room'])
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
        return $this->belongsToMany(Student::class,'class_offering_student');
    }

    public function classOfferingSubmissions(): HasMany
    {
        return $this->hasMany(ClassOfferingSubmission::class);
    }

    public function monthRecords(): HasMany
    {
        return $this->hasMany(StudentMonthRecord::class);
    }

    public function classOfferingDisciplines(): HasMany
    {
        return $this->hasMany(
            ClassOfferingDiscipline::class
        );
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            ScholarshipHolder::class,
            'class_offering_disciplines',
            'class_offering_id',
            'teacher_id'
        )
        ->wherePivotNotNull('teacher_id')
        ->distinct();
    }

    public function getTeachersCountAttribute(): int
    {
        return $this->teachers()
            ->distinct()
            ->count();
    }

    public function getDisciplinesCountAttribute(): int
    {
        return $this->classOfferingDisciplines()
            ->count();
    }

}
