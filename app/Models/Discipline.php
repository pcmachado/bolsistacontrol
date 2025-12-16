<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function classOfferings()
    {
        return $this->belongsToMany(
            ClassOffering::class,
            'class_offering_discipline',
            'discipline_id',
            'class_offering_id'
            )->withPivot(['teacher_id', 'workload', 'schedule', 'room'])
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'class_offering_discipline', 'discipline_id', 'teacher_id')
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

}
