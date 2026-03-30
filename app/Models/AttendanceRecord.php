<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scholarship_holder_id',
        'attendance_submission_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'calculated_value',
        'description',
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
            return 'draft';
        }

        return $this->submission->status;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->computed_status) {
            'draft'     => 'Em edição',
            'submitted' => 'Enviado',
            'approved'  => 'Homologado',
            'rejected'  => 'Rejeitado',
            default     => '-',
        };
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

        if ($this->submission->status === 'draft') {
            return true;
        }

        if ($this->submission->status === 'rejected') {
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