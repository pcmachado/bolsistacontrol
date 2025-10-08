<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_project';
    protected $fillable = ['course_id', 'project_id'];
}
