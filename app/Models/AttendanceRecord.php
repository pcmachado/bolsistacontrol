<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_LATE      = 'late';

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'scholarship_holder_id',
        'attendance_submission_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'calculated_value',
        'description',
        'status',
        'rejected_reason',
        'submitted_at',
        'approved_at',
        'approved_by_user_id',
        'rejected_at',
    ];

    protected $casts = [
        'date'         => 'date',
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

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AttendanceSubmission::class, 'attendance_submission_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('date', $month)
                     ->whereYear('date', $year);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeLate(Builder $query): Builder
    {
        return $query->whereIn('status', [
                    self::STATUS_DRAFT,
                    self::STATUS_SUBMITTED
                ])
                ->where('date', '<', now()->subDays(7));
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS METHODS
    |--------------------------------------------------------------------------
    */

    public function submit(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        $this->update([
            'status'       => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function approve(int $userId): void
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return;
        }

        $this->update([
            'status'               => self::STATUS_APPROVED,
            'approved_at'          => now(),
            'approved_by_user_id'  => $userId,
        ]);
    }

    public function reject(string $reason, int $userId): void
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return;
        }

        $this->update([
            'status'              => self::STATUS_REJECTED,
            'rejected_reason'     => $reason,
            'rejected_at'         => now(),
            'approved_by_user_id' => $userId,
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

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isEditable(): bool
    {
        if ($this->isDraft()) {
            return true;
        }

        if ($this->isRejected() && $this->rejected_at) {
            return now()->diffInDays($this->rejected_at) <= 7;
        }

        return false;
    }

    public function formattedDuration(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return '-';
        }

        $start = Carbon::parse($this->start_time);
        $end   = Carbon::parse($this->end_time);

        $minutes = $start->diffInMinutes($end);

        return sprintf('%02dh %02dm',
            floor($minutes / 60),
            $minutes % 60
        );
    }
}
