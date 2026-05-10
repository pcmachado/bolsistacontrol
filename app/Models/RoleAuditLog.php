<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAuditLog extends Model
{
    protected $fillable = [
        'role_id',
        'user_id',
        'action',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
