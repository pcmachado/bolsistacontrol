<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntelligentAlertSetting extends Model
{
    protected $fillable = [
        'no_class_days',
        'delay_percent_threshold',
        'check_delays_enabled',
        'check_no_class_enabled',
        'delay_notify_roles',
        'no_class_notify_roles',
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([]);
    }
}
