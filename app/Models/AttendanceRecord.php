<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['submitted_at', 'rejected_at', 'approved_at'];

    /**
     * Define o status inicial como rascunho.
     */
    public const STATUS_DRAFT = 'draft'; // Rascunho (pode ser editado pelo bolsista)
    public const STATUS_APPROVED = 'approved'; // Homologado pelo coordenador
    public const STATUS_REJECTED = 'rejected'; // Rejeitado (precisa de ajustes)
    public const STATUS_SUBMITTED = 'submitted'; // Enviado para homologação
    public const STATUS_LATE = 'late'; // Atrasado (não enviado em até 7 dias)

    protected $fillable = [
        'scholarship_holder_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'calculated_value',
        'description',
        'status',
        'submitted_at', // Data de envio para homologação
        'approved_at',
        'approved_by_user_id', // ID do usuário Coordenador que aprovou/rejeitou
        'rejected_reason',
        'rejected_at',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // --- RELACIONAMENTOS ---

    /**
     * O registro de frequência pertence a um Bolsista.
     */
    public function scholarshipHolder(): BelongsTo
    {
        return $this->belongsTo(ScholarshipHolder::class);
    }

    /**
     * O registro foi aprovado/rejeitado por um Coordenador Adjunto (User).
     */
    public function approver(): BelongsTo
    {
        // Relaciona-se com o Model User
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function scopeApproved($q) {
        return $q->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($q) {
        return $q->where('status', self::STATUS_REJECTED);
    }

    public function scopeDraft($q) {
        return $q->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted($q) {
        return $q->where('status', self::STATUS_SUBMITTED);
    }

    public function scopePending($q) {
        return $q->where('status', self::STATUS_SUBMITTED);
    }
    
    public function scopeByMonth($q, $month, $year) {
        return $q->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeLate($query)
    {
        return $query->whereIn('status', ['draft', 'submitted'])
                    ->where('date', '<', now()->subDays(7)); // atraso > 7 dias
    }

        public function isEditable(): bool
    {
        if ($this->status === 'draft') {
            return true;
        }

        if ($this->status === 'rejected' && $this->rejected_at) {
            return now()->diffInDays($this->rejected_at) <= 7;
        }

        return false;
    }

    public function formattedDuration(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return '-';
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);

        $diffInMinutes = $start->diffInMinutes($end);

        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    public function scopeByUserScope($query, $scope)
    {
        if ($scope['scope'] === 'all') {
            return $query;
        }

        if ($scope['scope'] === 'institution') {
            return $query->whereHas('scholarshipHolder', function ($q) use ($scope) {
                $q->where('institution_id', $scope['institution_id']);
            });
        }

        if ($scope['scope'] === 'unit') {
            return $query->whereHas('scholarshipHolder', function ($q) use ($scope) {
                $q->where('unit_id', $scope['unit_id']);
            });
        }

        if ($scope['scope'] === 'self') {
            return $query->where('scholarship_holder_id', auth()->user()->scholarshipHolder->id);
        }

        return $query->whereRaw('1 = 0'); // sem permissão
    }

    public function submit(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function approve(int $userId): void
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_user_id' => $userId,
        ]);
    }

    public function reject(string $reason, int $userId): void
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_reason' => $reason,
            'rejected_at' => now(),
            'approved_by_user_id' => $userId,
        ]);
    }

}
