<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRelease extends Model
{
    protected $fillable = [
        'version',
        'git_tag',
        'git_hash',
        'release_notes',
        'changes',
        'is_visible',
        'is_automatic',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'is_visible' => 'boolean',
            'is_automatic' => 'boolean',
            'released_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public static function normalizeVersion(string $version): string
    {
        $version = trim(strtolower($version));

        return 'v' . ltrim($version, 'v');
    }
}
