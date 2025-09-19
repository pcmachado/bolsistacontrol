<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['scholarship_holder_id', 'date', 'entry_time', 'exit_time', 'observations', 'hours', 'calculated_value', 'approved'];
    protected $casts = [ 'date' => 'date', 'entry_time' => 'datetime:H:i', 'exit_time' => 'datetime:H:i' ];

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }
}
