<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class StudentPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_offering_id',
        'month',
        'year',
        'amount',
        'status',
        'sent_at',
        'paid_at',
        'paid_by',
        'notes',
    ];

    /*
    |-----------------------------------------
    | STATUS
    |-----------------------------------------
    */

    public const STATUS_PENDING   = 'pending';
    public const STATUS_SENT      = 'sent';
    public const STATUS_PAID      = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    /*
    |-----------------------------------------
    | RELATIONS
    |-----------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /*
    |-----------------------------------------
    | HELPERS
    |-----------------------------------------
    */

    public function markAsPaid(int $userId): void
    {
        $this->update([
            'status'   => self::STATUS_PAID,
            'paid_at'  => now(),
            'paid_by'  => $userId,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status'  => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        if ($this->status !== self::STATUS_SENT || !$this->sent_at) {
            return false;
        }

        return Carbon::parse($this  ->sent_at)->diffInDays(now()) >= 10;
    }

    public function getComputedStatusAttribute(): string
    {
        if ($this->isPending()) {
            return self::STATUS_PENDING;
        }

        return $this->status;
    }
}