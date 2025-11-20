<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'city', 'state', 'address', 'phone'
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'institution_user')
            ->withPivot('active')
            ->withTimestamps();
    }
}
