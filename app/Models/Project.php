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
        'start_date',
        'end_date'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(institution::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'project_course')
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
        return $this->belongsToMany(FundingSource::class, 'project_funding_source')
                    ->withTimestamps();
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'project_scholarship_holder')
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
}
