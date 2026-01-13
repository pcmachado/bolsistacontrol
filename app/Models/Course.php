<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'duration_hours',
        'prerequisites',
        'start_date',
        'end_date',
        'active',
    ];

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'course_scholarship_holder')
                    ->withTimestamps();
    }

    public function disciplines()
    {
        return $this->belongsToMany(
            Discipline::class,
            'course_discipline',
            'course_id',
            'discipline_id'
        )->withTimestamps();
    }

    public function classOfferings()
    {
        return $this->hasMany(ClassOffering::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'supervisor_course_unit', 'course_id', 'supervisor_id')
                    ->withPivot('unit_id', 'active')
                    ->withTimestamps();
    }

    public function scopeVisibleForUser($query, $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            return $query->whereHas('classOfferings.unit', fn ($q) =>
                $q->where('institution_id', $user->institution_id)
            );
        }

        if ($user->unit_id) {
            return $query->whereHas('classOfferings', fn ($q) =>
                $q->where('unit_id', $user->unit_id)
            )
            ->orWhereDoesntHave('classOfferings');
        }

        return $query->whereRaw('1=0');
    }

}
