<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instituition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'city', 'state', 'address', 'phone'
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
