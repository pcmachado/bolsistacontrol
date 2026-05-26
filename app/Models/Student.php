<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'user_id',

        'name',

        'cpf',

        'passport',

        'payment_type',

        'pix_key',

        'bank',

        'agency',

        'account',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function studentRecords(): HasMany
    {
        return $this->hasMany(
            StudentRecord::class
        );
    }

    public function classOfferings(): BelongsToMany
    {
        return $this->belongsToMany(
            ClassOffering::class,
            'class_offering_student'
        )
        ->using(ClassOfferingStudent::class)
        ->withTimestamps();
    }
}