<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'institution_id',
        'student_daily_rate',
        'wizard_step',
        'status',
        'start_date',
        'end_date'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'project_position')
                    ->withPivot(['weekly_workload', 'hourly_rate'])
                    ->withTimestamps();
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(
                        FundingSource::class,
                        'project_funding_source',
                        'project_id',
                        'funding_source_id'
                    )
                    ->withPivot([
                        'amount',
                         'start_date',
                         'end_date',
                         'status',
                         ])
                    ->withTimestamps();
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(
                        ScholarshipHolder::class,
                        'project_scholarship_holder',
                        'project_id',
                        'scholarship_holder_id'
                    )
                    ->withPivot([
                        'position_id',
                        'weekly_workload',
                        'status',
                        'start_date',
                        'end_date',
                    ])
                    ->withTimestamps();
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'project_course',
            'project_id',
            'course_id'
        )->withPivot([
            'active',
            'semester',
            'year',
            'start_date',
            'end_date',
        ])->withTimestamps();
    }

    public function classOfferings(): HasMany
    {
        return $this->hasMany(ClassOffering::class);
    }

    public function units()
    {
        return $this->belongsToMany(
            Unit::class,
            'class_offerings',
            'project_id',
            'unit_id'
        )->distinct();
    }

    public function hourlyRateForScholarshipHolder(ScholarshipHolder $holder): float
    {
        $pivot = $this->scholarshipHolders()->where('scholarship_holder_id', $holder->id)->first()?->pivot;
        if (!$pivot) return 0;
        $positionId = $pivot->position_id;

        $positionPivot = $this->positions()->where('position_id', $positionId)->first()?->pivot;
        return (float)($positionPivot?->hourly_rate ?? 0);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
