<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'contact_info',
        'address',
        'total_amount',
        'used_amount',
        'start_date',
        'end_date',
        'active',
        'code',
    ];

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'float',
        'used_amount' => 'float',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_funding_source')
                    ->withTimestamps();
    }

    public function getAvailableAmountAttribute()
    {
        return $this->total_amount - $this->used_amount;
    }

    public function hasBalance(float $value): bool
    {
        return $this->available_amount >= $value;
    }

}
