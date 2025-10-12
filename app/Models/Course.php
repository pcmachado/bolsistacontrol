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

    public function projects(): HasMany
    {
        return $this->hasMany(CourseProject::class);
    }

    public function scholarshipHolders(): HasMany
    {
        return $this->hasMany(CourseScholarshipHolder::class);
    }
}
