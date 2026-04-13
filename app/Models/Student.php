<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'class_offering_id',
        'name',
        'cpf',
        'passport',
        'payment_type',
        'pix_key',
        'bank',
        'agency',
        'account'
    ];

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}