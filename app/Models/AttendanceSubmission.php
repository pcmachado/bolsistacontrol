<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class AttendanceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_LATE      = 'late';

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'scholarship_holder_id',
        'month',
        'year',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
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

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(
            AttendanceRecord::class,
            'attendance_submission_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForMonth(
        Builder $query,
        int $month,
        int $year
    ): Builder {
        return $query->where('month', $month)
                     ->where('year', $year);
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS METHODS
    |--------------------------------------------------------------------------
    */

    public function submit(): void
    {
        if (! $this->isDraft()) {
            return;
        }

        $this->update([
            'status'       => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function approve(int $userId): void
    {
        if (! $this->isSubmitted()) {
            return;
        }

        $this->update([
            'status'      => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function reject(string $reason, int $userId): void
    {
        if (! $this->isSubmitted()) {
            return;
        }

        $this->update([
            'status'          => self::STATUS_REJECTED,
            'rejected_reason' => $reason,
            'rejected_at'     => now(),
            'approved_by'     => $userId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }
}
