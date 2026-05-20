<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRelease extends Model
{
    protected $fillable = [
        'version',
        'release_notes',
    ];
}
