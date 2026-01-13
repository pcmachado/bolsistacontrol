<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_offering_id',
        'discipline_id',
        'date',
        'start_time',
        'end_time',
        'duration_hours',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function classOffering()
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
