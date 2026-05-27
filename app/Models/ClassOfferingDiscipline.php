<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassOfferingDiscipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'class_offering_disciplines';

    protected $fillable = [
        'class_offering_id',
        'discipline_id',
        'teacher_id',
        'workload',
        'planned_total_hours',
        'schedule',
        'room',
    ];

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class, 'teacher_id');
    }

    public function teacherScholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class, 'teacher_scholarship_holder_id');
    }
}

