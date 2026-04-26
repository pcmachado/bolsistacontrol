<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ScholarshipHolder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinalActivityReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'scholarship_holder_id',
        'project_id',
        'start_date',
        'end_date',
        'activities',
        'results',
        'contributions',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';

    public function scholarshipHolder()
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
