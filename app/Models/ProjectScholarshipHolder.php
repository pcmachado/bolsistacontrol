<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectScholarshipHolder extends Pivot
{

    use HasFactory, SoftDeletes;
    
    protected $table = 'project_scholarship_holders';

    protected $fillable = [
        'project_id', 'scholarship_holder_id', 'position_id', 'monthly_workload', 'start_date'
    ];
}