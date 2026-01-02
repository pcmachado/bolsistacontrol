<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
    protected $fillable = [
        'action',
        'entity_type',
        'entity_id',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
