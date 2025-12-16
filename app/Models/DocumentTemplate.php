<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'description',
        'header_html',
        'body_html',
        'footer_html',
        'institution_id',
        'unit_id',
        'active',
    ];

    public static function for(string $key, $unitId = null, $institutionId = null)
    {
        return self::where('key', $key)
            ->where('active', true)
            ->where(function ($q) use ($unitId, $institutionId) {
                $q->whereNull('unit_id')->orWhere('unit_id', $unitId);
                $q->whereNull('institution_id')->orWhere('institution_id', $institutionId);
            })
            ->orderByDesc('unit_id')
            ->orderByDesc('institution_id')
            ->first();
    }
}
