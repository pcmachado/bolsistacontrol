<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    // Status possíveis
    public const STATUS_DRAFT          = 'draft';
    public const STATUS_SENT           = 'sent_to_payment';
    public const STATUS_PAID           = 'paid';
    public const STATUS_CONFIRMED      = 'confirmed';

    protected $fillable = [
        'scholarship_holder_id',
        'project_id',
        'unit_id',
        'month',
        'year',
        'total_hours',
        'amount',
        'status',
        'sent_at',
        'paid_at',
        'confirmed_at',
        'paid_by_user_id',
        'notes',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'paid_at'      => 'datetime',
        'confirmed_at' => 'datetime',
        'total_hours'  => 'float',
        'amount'       => 'float',
    ];

    /* RELACIONAMENTOS */

    public function scholarshipHolder()
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    /* HELPERS DE STATUS */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function periodLabel(): string
    {
        return str_pad($this->month, 2, '0', STR_PAD_LEFT) . '/' . $this->year;
    }

    public static function generateReceiptNumber()
    {
        $prefix = now()->format('Ym'); // 202502
        $hash = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)); // 6 chars

        return $prefix . '-' . $hash;
    }

}
