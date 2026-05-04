<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent_to_payment';

    public const STATUS_PAID = 'paid';

    public const STATUS_CONFIRMED = 'confirmed';

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'scholarship_holder_id',
        'project_id',
        'unit_id',
        'funding_source_id',
        'attendance_submission_id',
        'month',
        'year',
        'total_hours',
        'amount',
        'status',
        'sent_at',
        'paid_at',
        'confirmed_at',
        'paid_by_user_id',
        'receipt_number',
        'receipt_generated_at',
        'receipt_hash',
        'notes',
        'payable_id',
        'payable_type',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'total_hours' => 'float',
        'amount' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS METHODS
    |--------------------------------------------------------------------------
    */

    public function send(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsPaid(int $userId): void
    {
        if ($this->status !== self::STATUS_SENT) {
            return;
        }

        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'paid_by_user_id' => $userId,
        ]);
    }

    public function confirm(): void
    {
        if ($this->status !== self::STATUS_PAID) {
            return;
        }

        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

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
        return str_pad($this->month, 2, '0', STR_PAD_LEFT)
            .'/'
            .$this->year;
    }

    public function safePeriodLabel(): string
    {
        return str_replace('/', '-', $this->periodLabel());
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIPT
    |--------------------------------------------------------------------------
    */

    public static function generateReceiptNumber(): string
    {
        return now()->format('Ym')
            .'-'
            .strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    public static function generateReceiptHash(self $payment): string
    {
        $base = implode('|', [
            $payment->id,
            $payment->scholarship_holder_id,
            $payment->project_id,
            $payment->amount,
            $payment->month,
            $payment->year,
            optional($payment->paid_at)->toDateTimeString(),
        ]);

        return hash('sha256', $base);
    }
}
