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
        'instituition_id',
        'start_date',
        'end_date'
    ];

    public function instituition(): BelongsTo
    {
        return $this->belongsTo(Instituition::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(ProjectCourse::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'project_positions')
                    ->withTimestamps();
    }

    public function fundingSources(): HasMany
    {
        return $this->hasMany(ProjectFundingSource::class);
    }

    public function scholarshipHolders(): HasMany
    {
        return $this->hasMany(ProjectScholarshipHolder::class);
    }
}
