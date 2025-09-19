<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'institution_id', 'start_date', 'end_date'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function scholarshipHolders()
    {
        return $this->belongsToMany(ScholarshipHolder::class, 'project_scholarship_holders')
                    ->withPivot(['position_id', 'monthly_workload', 'start_date'])
                    ->withTimestamps();
    }
}
