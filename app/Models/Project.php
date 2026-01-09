<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'institution_id',
        'wizard_step',
        'start_date',
        'end_date'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(institution::class);
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

    public function classOfferings()
    {
        return $this->hasManyThrough(
            ClassOffering::class,
            Course::class,
            'project_id', // FK em courses
            'course_id'   // FK em class_offerings
        );
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
            );
        }

        return $query->whereRaw('1=0');
    }

    public function scopeByUserInstitution($query, $user)
    {
        // Admin vê tudo
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Coordenadores → só projetos da instituição
        if (
            $user->hasRole('coordenador_geral') ||
            $user->hasRole('coordenador_adjunto_geral') ||
            $user->hasRole('coordenador_adjunto')
        ) {
            return $query->where('institution_id', $user->institution_id);
        }

        // Outros perfis → nada
        return $query->whereRaw('1 = 0');
    }

}
