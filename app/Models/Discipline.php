<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'name',
        'workload',
        'sequence_order',
        'active',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_discipline',
            'discipline_id',
            'course_id'
        )->withTimestamps();
    }

    public function classOfferings(): BelongsToMany
    {
        return $this->belongsToMany(
            ClassOffering::class,
            'class_offering_disciplines',
            'discipline_id',
            'class_offering_id'
            )->withPivot(['teacher_id', 'workload', 'planned_total_hours', 'hours_per_day', 'schedule', 'room'])
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
}
