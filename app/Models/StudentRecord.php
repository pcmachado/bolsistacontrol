<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'student_id',
        'class_offering_id',
        'total_classes',
        'absences',
        'justified_absences',
        'daily_rate',
        'status',
        'attended_classes',
        'total_amount',
        'submitted_at',
        'approved_at',
        'status_financial',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function calculate(): void
    {
        $this->attended_classes = max(0, $this->total_classes - $this->absences);
        $this->total_amount = $this->attended_classes * $this->daily_rate;
    }

    public function recalculateFromMonths()
    {
        $months = StudentMonthRecord::where([
            'student_id' => $this->student_id,
            'class_offering_id' => $this->class_offering_id,
        ])->get();

        $this->absences = $months->sum('absences');

        $this->attended_classes = max(0, $this->total_classes - $this->absences);

        $this->save();
    }
}
