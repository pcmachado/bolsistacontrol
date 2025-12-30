<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Http\Traits\BelongsToInstitution;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    use BelongsToInstitution;

    protected $fillable = [
        'name',
        'description',
        'institution_id',
        'unit_id',
        'wizard_step',
        'start_date',
        'end_date'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(institution::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
                        Course::class,
                        'class_offerings',
                        'project_id',
                        'course_id'
                        )
                    ->withPivot([
                        'semester',
                        'year',
                        'active',
                        'start_date',
                        'end_date',
                    ])
                    ->withTimestamps();
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
