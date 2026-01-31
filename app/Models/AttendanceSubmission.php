<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['submitted_at', 'reviewed_at'];

    /**
     * Define o status inicial como rascunho.
     */
    public const STATUS_DRAFT = 'draft'; // Rascunho (pode ser editado pelo bolsista)
    public const STATUS_SUBMITTED = 'submitted'; // Em análise pelo coordenador
    public const STATUS_APPROVED = 'approved'; // Homologado pelo coordenador
    public const STATUS_REJECTED = 'rejected'; // Rejeitado (precisa de ajustes)
    public const STATUS_LATE = 'late'; // Atrasado (não enviado em até 7 dias)

    protected $fillable = [
        'scholarship_holder_id',
        'month',
        'year',
        'status',
        'submitted_at', // Data de envio para análise
        'approved_at',
        'approved_by', // ID do usuário Coordenador que aprovou/rejeitou
        'rejected_at',
        'rejected_reason',
        'timestamps',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // --- RELACIONAMENTOS ---

    /**
     * A submissão de frequência pertence a um Bolsista.
     */
    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    /**
     * A submissão de frequência pode ter muitos registros de frequência.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function scopeForMonth($q, int $month, int $year)
    {
        return $q->where('month', $month)->where('year', $year);
    }

    /* =======================
     |  STATUS HELPERS
     |=======================*/
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isLate(): bool
    {
        return $this->status === 'late';
    }

}