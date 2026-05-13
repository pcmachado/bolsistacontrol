<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'shortname',
        'city',
        'state',
        'address',
        'phone',
        'email',
        'cnpj',
        'website',
        'acronym',
        'contact_person',
        'contact_email',
        'contact_phone',
        'logo_path',
        'postal_code',
        'neighborhood',
        'complement',
        'number',
        'country',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'institution_user')
            ->withPivot('active')
            ->withTimestamps();
    }
}
