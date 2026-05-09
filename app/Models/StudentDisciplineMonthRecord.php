<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDisciplineMonthRecord extends Model
{
    protected $fillable = [
        'student_id',
        'class_offering_id',
        'discipline_id',
        'month',
        'year',
        'total_classes',
        'absences',
        'justified_absences',
        'attended_classes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function classOffering()
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function calculate(): void
    {
        $this->attended_classes = max(
            0,
            $this->total_classes - $this->absences
        );
    }
}
