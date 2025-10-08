<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_funding_source');
    }
}
