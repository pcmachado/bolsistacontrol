<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AttendanceSubmission;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scholarship_holder_id',
        'attendance_submission_id',
        'project_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'description',
        'has_issue',
        'issue_reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /*
    |------------------------------------------------------------------
    | RELATIONSHIPS
    |------------------------------------------------------------------
    */

    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AttendanceSubmission::class, 'attendance_submission_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /*
    |------------------------------------------------------------------
    | SCOPES
    |------------------------------------------------------------------
    */

    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('date', $month)
            ->whereYear('date', $year);
    }

    /*
    |------------------------------------------------------------------
    | STATUS DERIVADO (CORE DO SISTEMA)
    |------------------------------------------------------------------
    */

    public function getComputedStatusAttribute(): string
    {
        if (! $this->submission) {
            return AttendanceSubmission::STATUS_DRAFT;
        }

        return $this->submission->status;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->computed_status) {
            AttendanceSubmission::STATUS_DRAFT => 'Rascunho',
            AttendanceSubmission::STATUS_SUBMITTED => 'Enviado',
            AttendanceSubmission::STATUS_APPROVED => 'Aprovado',
            AttendanceSubmission::STATUS_REJECTED => 'Rejeitado',
            default => ucfirst($this->computed_status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return sprintf(
            '<span class="badge bg-%s">%s</span>',
            $this->status_color,
            $this->status_label
        );
    }

    public function getStatusColorAttribute(): string
    {
        return status_color($this->computed_status);
    }

    /*
    |------------------------------------------------------------------
    | REGRAS DE NEGÓCIO
    |------------------------------------------------------------------
    */

    public function isEditable(): bool
    {
        if (! $this->submission) {
            return true;
        }

        if ($this->submission->status === AttendanceSubmission::STATUS_DRAFT) {
            return true;
        }

        if ($this->submission->status === AttendanceSubmission::STATUS_REJECTED) {
            return $this->submission->rejected_at
                ? now()->diffInDays($this->submission->rejected_at) <= 7
                : true;
        }

        return false;
    }

    /*
    |------------------------------------------------------------------
    | HELPERS
    |------------------------------------------------------------------
    */

    public function formattedDuration(): string
    {
        if (! $this->start_time || ! $this->end_time) {
            return '-';
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        $minutes = $start->diffInMinutes($end);

        return sprintf('%02dh %02dm',
            floor($minutes / 60),
            $minutes % 60
        );
    }

    public function editBlockReason(): ?string
    {
        if (! $this->submission) {
            return null;
        }

        if ($this->submission->status === AttendanceSubmission::STATUS_REJECTED) {
            if ($this->submission->rejected_at &&
                now()->diffInDays($this->submission->rejected_at) > 7) {
                return 'Prazo de 7 dias após rejeição expirado.';
            }
        }

        if (in_array($this->submission->status, [AttendanceSubmission::STATUS_SUBMITTED, AttendanceSubmission::STATUS_APPROVED])) {
            return 'Registro já enviado e não pode ser alterado.';
        }

        return null;
    }

    public function getMonthAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('Y-m') : now()->format('Y-m');
    }
}
