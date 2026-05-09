<?php

namespace App\Models;

use App\Models\Scopes\InstitutionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'name',
        'shortname',
        'city',
        'address',
        'phone',
        'domain',
        'email',
        'cnpj',
        'is_administrative',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Os utilizadores que pertencem a esta unidade.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Os bolsistas que pertencem a esta unidade.
     */
    public function scholarshipHolders(): HasMany
    {
        return $this->hasMany(ScholarshipHolder::class);
    }

    public function classOfferings()
    {
        return $this->hasMany(ClassOffering::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new InstitutionScope);
    }

    public function isAdministrative(): bool
    {
        return (bool) $this->is_administrative;
    }
}
