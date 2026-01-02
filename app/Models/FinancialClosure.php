<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialClosure extends Model
{
    protected $fillable = [
        'unit_id',
        'month',
        'year',
        'closed_at',
        'closed_by_user_id',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public static function isClosed($unitId, $month, $year): bool
    {
        return self::where('unit_id', $unitId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }
}
