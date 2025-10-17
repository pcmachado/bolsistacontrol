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

    protected $fillable = [
        'institution_id',
        'name',
        'city',
        'address',
        'phone',
        'email',
        'cnpj'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(institution::class);
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
}
