<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\NotificationSetting;
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
     * Envia notificação baseada em configurações e templates
     */
    public function sendEventNotification(
        string $eventType,
        array $data,
        ?int $projectId = null,
        ?int $institutionId = null
    ): void {
        $setting = NotificationSetting::findByEvent($eventType, $projectId, $institutionId);

        if (! $setting) {
            return; // nenhuma configuração encontrada
        }

        $recipients = $this->resolveRecipients($setting->recipients, $projectId, $institutionId);

        if ($setting->shouldSendDatabase()) {
            $this->sendDatabaseNotifications($eventType, $data, $recipients);
        }

        if ($setting->shouldSendEmail()) {
            $this->sendEmailNotifications($eventType, $data, $recipients, $projectId, $institutionId);
        }
    }

    /**
     * Resolve os destinatários baseado na configuração
     */
    private function resolveRecipients(array $recipientsConfig, ?int $projectId, ?int $institutionId): Collection
    {
        $users = collect();

        foreach ($recipientsConfig as $recipient) {
            if (isset($recipient['type'])) {
                switch ($recipient['type']) {
                    case 'role':
                        $roleUsers = User::role($recipient['value'])->get();
                        $users = $users->merge($roleUsers);
                        break;
                    case 'user':
                        if ($user = User::find($recipient['value'])) {
                            $users->push($user);
                        }
                        break;
                    case 'project_coordinator':
                        if ($projectId) {
                            // lógica para buscar coordenadores do projeto
                            $projectUsers = User::whereHas('projects', function ($q) use ($projectId) {
                                $q->where('projects.id', $projectId);
                            })->get();
                            $users = $users->merge($projectUsers);
                        }
                        break;
                }
            }
        }

        return $users->unique('id');
    }

    /**
     * Envia notificações no banco de dados
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
     * Envia notificações por email usando templates
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

    /**
     * Retorna a classe de notificação baseada no tipo de evento
     */
    private function getNotificationClass(string $eventType): ?string
    {
        return match ($eventType) {
            'payment_status_changed' => \App\Notifications\PaymentStatusChanged::class,
            'submission_approved' => \App\Notifications\AttendanceApproved::class,
            'submission_rejected' => \App\Notifications\AttendanceRejected::class,
            'submission_submitted' => \App\Notifications\AttendanceSubmitted::class,
            default => null,
        };
    }
}
