<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectFundingSource extends pivot
{
    use HasFactory;

    protected $table = 'project_funding_sources';

    protected $fillable = [
        'project_id',
        'funding_source_id',
        'amount',
        'start_date',
        'end_date',
        'status'
    ];  

    /**
     * Relacionamento: pertence a um projeto.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relacionamento: pertence a uma fonte pagadora.
     */
    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }
}
