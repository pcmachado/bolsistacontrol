<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'notification_type',
        'recipients',
        'project_id',
        'institution_id',
        'enabled',
    ];

    protected $casts = [
        'recipients' => 'array',
        'enabled' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Busca configuração por tipo de evento, priorizando projeto específico
     */
    public static function findByEvent(string $eventType, ?int $projectId = null, ?int $institutionId = null): ?self
    {
        return self::query()
            ->where('event_type', $eventType)
            ->where('enabled', true)
            ->where(function ($query) use ($projectId, $institutionId) {
                $query->where(function ($q) use ($projectId) {
                    $q->where('project_id', $projectId)->orWhereNull('project_id');
                })->where(function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId)->orWhereNull('institution_id');
                });
            })
            ->orderByDesc('project_id')
            ->orderByDesc('institution_id')
            ->first();
    }

    /**
     * Verifica se deve enviar notificação por email
     */
    public function shouldSendEmail(): bool
    {
        return in_array($this->notification_type, ['mail', 'both']);
    }

    /**
     * Verifica se deve enviar notificação no banco
     */
    public function shouldSendDatabase(): bool
    {
        return in_array($this->notification_type, ['database', 'both']);
    }

    /**
     * Retorna o label do tipo de evento
     */
    public function getEventTypeLabel(): string
    {
        $labels = [
            'payment_status_changed' => 'Mudança de Status de Pagamento',
            'submission_submitted' => 'Submissão de Frequência Enviada',
            'submission_approved' => 'Submissão de Frequência Aprovada',
            'submission_rejected' => 'Submissão de Frequência Rejeitada',
        ];

        return $labels[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }
}
