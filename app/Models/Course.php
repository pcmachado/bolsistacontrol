<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'duration_hours',
        'prerequisites',
        'start_date',
        'end_date',
        'active',
    ];

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'course_scholarship_holder')
                    ->withTimestamps();
    }

    public function disciplines()
    {
        return $this->belongsToMany(
            Discipline::class,
            'course_discipline',
            'course_id',
            'discipline_id'
        )->withTimestamps();
    }

    public function classOfferings()
    {
        return $this->hasMany(ClassOffering::class);
    }

    public function units()
    {
        return $this->hasManyThrough(Unit::class, ClassOffering::class);
    }

    public function projects()
    {
        return $this->hasManyThrough(Project::class, ClassOffering::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'supervisor_course_unit', 'course_id', 'supervisor_id')
                    ->withPivot('unit_id', 'active')
                    ->withTimestamps();
    }

}
