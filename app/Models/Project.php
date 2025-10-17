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
        'start_date',
        'end_date'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(institution::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'project_courses')
                    ->withTimestamps();
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'project_positions')
                    ->withTimestamps();
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'project_funding_sources')
                    ->withTimestamps();
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'project_scholarship_holders')
                    ->withTimestamps();
    }
}
