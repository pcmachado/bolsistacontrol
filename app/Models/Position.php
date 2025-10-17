<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_positions')
                    ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'position_scholarship_holder')
                    ->withTimestamps();
    }
}
