<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'class_offering_discipline')
            ->withPivot(['teacher_id', 'workload', 'schedule', 'room'])
            ->withTimestamps();
    }

    public function scholarshipHolders()
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'scholarship_holder_class_offering')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

}
