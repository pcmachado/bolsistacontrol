<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassOfferingStudent extends Pivot
{
    protected $table = 'class_offering_student';

    protected $fillable = [

        'student_id',

        'class_offering_id',
    ];

    public $timestamps = true;

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(
            ClassOffering::class
        );
    }
}