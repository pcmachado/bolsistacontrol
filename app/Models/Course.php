<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'course_project');
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'course_scholarship_holder');
    }
}
