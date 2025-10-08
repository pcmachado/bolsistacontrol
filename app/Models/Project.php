<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'instituition_id', 'start_date', 'end_date'
    ];

    public function instituition()
    {
        return $this->belongsTo(Instituition::class);
    }

    public function scholarshipHolders()
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'project_scholarship_holders')
                    ->withPivot(['position_id', 'monthly_workload', 'start_date'])
                    ->withTimestamps();
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_project');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'project_funding_source');
    }
}
