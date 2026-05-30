<?php

namespace App\Models;

use App\Models\Concerns\HasStatusPresentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassOfferingSubmission extends Model
{
    use HasFactory, HasStatusPresentation, SoftDeletes;
    protected $fillable = [
        'id',
        'class_offering_id',
        'total_students',
        'total_amount',
        'status',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'rejected_reason',
        'month',
        'year'
    ];

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }
    
    public function submissionFor(int $month, int $year): HasOne
    {
        return $this->hasOne(ClassOfferingSubmission::class)
            ->where('month', $month)
            ->where('year', $year);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
