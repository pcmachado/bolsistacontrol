<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceRejected extends Notification
{
    use Queueable;

    public function __construct(protected array $data) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Submissão de Frequência Rejeitada',
            'message' => $this->data['message'] ?? 'Sua submissão de frequência foi rejeitada.',
            'submission_id' => $this->data['submission_id'] ?? null,
            'month' => $this->data['month'] ?? null,
            'year' => $this->data['year'] ?? null,
            'reason' => $this->data['reason'] ?? null,
            'url' => $this->data['url'] ?? null,
            'level' => $this->data['level'] ?? 'danger',
        ];
    }
}
