<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['instituition_id', 'name', 'city', 'address'];

    public function instituitions(): BelongsTo
    {
        return $this->belongsTo(Instituition::class);
    }  

    public function scholarshipHolders(): HasMany
    {
        return $this->hasMany(ScholarshipHolder::class, 'unit_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
