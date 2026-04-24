<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipHolderClassOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_holder_id',
        'class_offering_id',
        'role',
    ];

    public function scholarshipHolder()
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function classOffering()
    {
        return $this->belongsTo(ClassOffering::class);
    }
}