<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class);
    }

    public function classOfferings(): HasMany
    {
        return $this->hasMany(ClassOffering::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_course',
            'course_id',
            'project_id'
        )->withPivot([
            'active',
            'semester',
            'year',
            'start_date',
            'end_date',
        ])->withTimestamps();
    }

}
