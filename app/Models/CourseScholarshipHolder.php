<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseScholarshipHolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_scholarship_holder';
    protected $fillable = ['course_id', 'scholarship_holder_id'];
}
