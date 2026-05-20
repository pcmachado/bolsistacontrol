<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRelease extends Model
{
    protected $fillable = [
        'version',
        'release_notes',
    ];

    public static function normalizeVersion(string $version): string
    {
        $version = trim(strtolower($version));

        return 'v' . ltrim($version, 'v');
    }
}
