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
        'observation',
        'status',
        'submitted_at', // Data de envio para homologação
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
}
