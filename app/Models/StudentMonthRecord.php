<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMonthRecord extends Model
{
    protected $fillable = [
        'student_id',
        'class_offering_id',
        'month',
        'year',
        'absences',
        'attended_classes',
        'total_classes',
        'total_absences',
        'total_justified_absences',
        'total_presences',
        'estimated_payment_amount',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }
}