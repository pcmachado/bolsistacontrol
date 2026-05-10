<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LATE = 'late';

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'scholarship_holder_id',
        'project_id',
        'month',
        'year',
        'status',
        'total_hours',
        'calculated_value',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

        if ($this->isLocked()) {
            return;
        }

        $this->recalculate();

        app(\App\Services\PaymentGenerationService::class)->generateFromSubmission($this);

        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function approve(int $userId): void
    {
        if (! $this->isSubmitted()) {
            return;
        }

        if ($this->isLocked()) {
            return;
        }

        $this->recalculate();

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function reject(string $reason, int $userId): void
    {
        if (! $this->isSubmitted()) {
            return;
        }

        if ($this->isLocked()) {
            return;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_reason' => $reason,
            'rejected_at' => now(),
            'approved_by' => $userId,
        ]);

        $this->attendanceRecords()
            ->where('has_issue', false)
            ->update([
                'has_issue' => true,
                'issue_reason' => $reason,
            ]);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recalculate(): void
    {
        if ($this->isApproved()) {
            return;
        }

        if ($this->isLocked()) {
            return;
        }

        $hours = $this->attendanceRecords()->sum('hours');

        $holder = $this->scholarshipHolder;
        $project = $this->relationLoaded('project')
            ? $this->project
            : $this->project()->with('positions')->first();
        $rate = $project?->hourlyRateForScholarshipHolder($holder) ?? 0;

        $this->update([
            'total_hours' => $hours,
            'calculated_value' => $hours * $rate,
        ]);
    }

    protected function calculateValue(float $hours): float
    {
        $hourValue = $this->relationLoaded('scholarshipHolder')
            ? $this->scholarshipHolder->hour_value
            : $this->scholarshipHolder()->value('hour_value');

        return round($hours * $hourValue, 2);
    }

    public function getPeriodAttribute(): string
    {
        return sprintf('%02d/%d', $this->month, $this->year);
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

    public function isLocked(): bool
    {
        return \App\Models\FinancialClosure::isClosed(
            $this->scholarshipHolder->unit_id,
            $this->month,
            $this->year
        );
    }
}
