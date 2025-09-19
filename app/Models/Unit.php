<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['institution_id', 'name', 'city', 'address'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }  

    public function scholarshipHolders(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'scholarship_holder_units');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_unit', 'unit_id', 'user_id');
    }
}
