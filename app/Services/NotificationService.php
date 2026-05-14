<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\EmailTemplate;
use App\Models\NotificationSetting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function send(User $user, Notification $notification): void
    {
        $user->notify($notification);
    }

    public function sendToMany($users, Notification $notification): void
    {
        foreach ($users as $user) {
            $user->notify(clone $notification);
        }
    }

    /**
     * Envia notificação baseada em configurações e templates.
     */
    public function sendEventNotification(
        string $eventType,
        array $data,
        ?int $projectId = null,
        ?int $institutionId = null
    ): void {
        $setting = NotificationSetting::findByEvent($eventType, $projectId, $institutionId);
        $defaultRecipients = $this->defaultRecipientsFor($eventType);
        $recipientsConfig = $defaultRecipients !== []
            ? $defaultRecipients
            : ($setting?->recipients ?? []);

        if ($recipientsConfig === []) {
            return;
        }

        $recipients = $this->resolveRecipients($recipientsConfig, $projectId, $institutionId, $data);

        if ($recipients->isEmpty()) {
            return;
        }

        if ($setting?->shouldSendDatabase() ?? true) {
            $this->sendDatabaseNotifications($eventType, $data, $recipients);
        }

        if ($setting?->shouldSendEmail() ?? false) {
            $this->sendEmailNotifications($eventType, $data, $recipients, $projectId, $institutionId);
        }
    }

    /**
     * Resolve os destinatários baseado na configuração e no contexto do evento.
     */
    private function resolveRecipients(
        array $recipientsConfig,
        ?int $projectId,
        ?int $institutionId,
        array $data = []
    ): Collection {
        $users = collect();

        foreach ($recipientsConfig as $recipient) {
            if (is_string($recipient)) {
                $recipient = str_contains($recipient, ':')
                    ? array_combine(['type', 'value'], explode(':', $recipient, 2))
                    : ['type' => 'role', 'value' => $recipient];
            }

            if (! isset($recipient['type'])) {
                continue;
            }

            $users = $users->merge(match ($recipient['type']) {
                'role' => $this->usersByRole($recipient['value'], $institutionId),
                'user' => ($user = User::find($recipient['value'])) ? collect([$user]) : collect(),
                'project_coordinator' => $this->projectCoordinators($projectId, $institutionId),
                'unit_coordinator' => $this->unitCoordinators($data['unit_id'] ?? null, $institutionId),
                'administrative_general_coordinator' => $this->administrativeGeneralCoordinators($data, $institutionId),
                'submission_owner' => $this->usersByIds([$data['scholarship_holder_user_id'] ?? $data['submitter_user_id'] ?? null]),
                'financial_coordinator' => $this->financialCoordinators($institutionId),
                default => collect(),
            });
        }

        return $users->filter()->unique('id')->values();
    }

    private function usersByRole(string $role, ?int $institutionId = null): Collection
    {
        return User::role($role)
            ->when($institutionId, fn ($query) => $query->where(function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId)
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('institution_id', $institutionId))
                    ->orWhereHas('institutions', fn ($institutionQuery) => $institutionQuery->where('institutions.id', $institutionId))
                    ->orWhereHas('assignments.unit', fn ($unitQuery) => $unitQuery->where('institution_id', $institutionId));
            }))
            ->get();
    }

    private function usersByIds(array $userIds): Collection
    {
        $ids = collect($userIds)->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $ids)->get();
    }

    private function projectCoordinators(?int $projectId, ?int $institutionId = null): Collection
    {
        if (! $projectId) {
            return collect();
        }

        return User::query()
            ->whereHas('assignments', function ($query) use ($projectId) {
                $query->where('active', true)
                    ->where('project_id', $projectId)
                    ->whereIn('assignment_type', [
                        Assignment::TYPE_COORDENADOR_GERAL,
                        Assignment::TYPE_COORDENADOR_ADJUNTO_GERAL,
                        Assignment::TYPE_COORDENADOR_ADJUNTO,
                    ]);
            })
            ->when($institutionId, fn ($query) => $query->where(function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId)
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('institution_id', $institutionId))
                    ->orWhereHas('institutions', fn ($institutionQuery) => $institutionQuery->where('institutions.id', $institutionId));
            }))
            ->get();
    }

    private function unitCoordinators(?int $unitId, ?int $institutionId = null): Collection
    {
        if (! $unitId) {
            return collect();
        }

        return User::role('coordenador_adjunto')
            ->where(function ($query) use ($unitId) {
                $query->where('unit_id', $unitId)
                    ->orWhereHas('assignments', function ($assignmentQuery) use ($unitId) {
                        $assignmentQuery->where('active', true)
                            ->where('unit_id', $unitId)
                            ->where('assignment_type', Assignment::TYPE_COORDENADOR_ADJUNTO);
                    });
            })
            ->when($institutionId, fn ($query) => $query->where(function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId)
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('institution_id', $institutionId))
                    ->orWhereHas('institutions', fn ($institutionQuery) => $institutionQuery->where('institutions.id', $institutionId));
            }))
            ->get();
    }

    private function administrativeGeneralCoordinators(array $data, ?int $institutionId = null): Collection
    {
        $unit = isset($data['unit_id']) ? Unit::withoutGlobalScopes()->find($data['unit_id']) : null;
        $submitter = isset($data['submitter_user_id']) ? User::find($data['submitter_user_id']) : null;

        $isAdministrativeSubmission = $unit?->isAdministrative()
            || $submitter?->hasAnyRole(['coordenador_adjunto', 'coordenador_adjunto_geral']);

        if (! $isAdministrativeSubmission) {
            return collect();
        }

        return $this->usersByRole('coordenador_geral', $institutionId ?? $unit?->institution_id);
    }

    private function financialCoordinators(?int $institutionId = null): Collection
    {
        return User::role('coordenador_adjunto_geral')
            ->when($institutionId, fn ($query) => $query->where(function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId)
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('institution_id', $institutionId))
                    ->orWhereHas('institutions', fn ($institutionQuery) => $institutionQuery->where('institutions.id', $institutionId));
            }))
            ->get();
    }

    /**
     * Envia notificações no banco de dados.
     */
    private function sendDatabaseNotifications(string $eventType, array $data, Collection $recipients): void
    {
        $notificationClass = $this->getNotificationClass($eventType);

        if (! $notificationClass) {
            return;
        }

        foreach ($recipients as $user) {
            $user->notify(new $notificationClass($data));
        }
    }

    /**
     * Envia notificações por email usando templates.
     */
    private function sendEmailNotifications(
        string $eventType,
        array $data,
        Collection $recipients,
        ?int $projectId,
        ?int $institutionId
    ): void {
        $template = EmailTemplate::findByKey($eventType, $projectId, $institutionId);

        if (! $template) {
            return;
        }

        $rendered = $template->render($data);

        foreach ($recipients as $user) {
            Mail::send([], [], function ($message) use ($user, $rendered) {
                $message->to($user->email)
                    ->subject($rendered['subject'])
                    ->html($rendered['body_html']);

                if ($rendered['body_text']) {
                    $message->text($rendered['body_text']);
                }
            });
        }
    }

    private function defaultRecipientsFor(string $eventType): array
    {
        return match ($eventType) {
            'submission_submitted' => [
                ['type' => 'unit_coordinator'],
                ['type' => 'administrative_general_coordinator'],
            ],
            'submission_approved', 'submission_rejected' => [
                ['type' => 'submission_owner'],
            ],
            'payment_sent_to_financial' => [
                ['type' => 'financial_coordinator'],
            ],
            'payment_status_changed' => [
                ['type' => 'financial_coordinator'],
            ],
            default => [],
        };
    }

    /**
     * Retorna a classe de notificação baseada no tipo de evento.
     */
    private function getNotificationClass(string $eventType): ?string
    {
        return match ($eventType) {
            'payment_status_changed', 'payment_sent_to_financial' => \App\Notifications\PaymentStatusChanged::class,
            'submission_approved' => \App\Notifications\AttendanceApproved::class,
            'submission_rejected' => \App\Notifications\AttendanceRejected::class,
            'submission_submitted' => \App\Notifications\AttendanceSubmitted::class,
            default => null,
        };
    }
}
