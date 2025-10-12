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

    protected $dates = ['submitted_at', 'rejected_at'];

    /**
     * Define o status inicial como rascunho.
     */
    public const STATUS_DRAFT = 'draft'; // Rascunho (pode ser editado pelo bolsista)
    public const STATUS_PENDING = 'pending'; // Pendente de aprovação
    public const STATUS_APPROVED = 'approved'; // Homologado pelo coordenador
    public const STATUS_REJECTED = 'rejected'; // Rejeitado (precisa de ajustes)

    protected $fillable = [
        'scholarship_holder_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'calculated_value',
        'observation',
        'status',
        'submitted_at', // Data de envio para homologação
        'approved',
        'approved_by_user_id', // ID do usuário Coordenador que aprovou/rejeitou
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
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
        return $q->where('status', 'approved');
    }

    public function scopeRejected($q) { 
        return $q->where('status', 'rejected'); 
    }

    public function scopeDraft($q) {
        return $q->where('status', 'draft');
    }

    public function scopePending($q) {
        return $q->where('status', 'pending');
    }

    public function scopeSubmitted($q) {
        return $q->where('status', 'submitted');
    }
    
    public function scopeByMonth($q, $month, $year) {
        return $q->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeLate($query)
    {
        return $query->whereIn('status', ['draft', 'pending'])
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
}
