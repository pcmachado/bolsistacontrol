<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceApproved extends Notification
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
            'title' => $this->data['title'] ?? 'Submissão de Frequência Aprovada',
            'message' => $this->data['message'] ?? 'Sua submissão de frequência foi aprovada.',
            'submission_id' => $this->data['submission_id'] ?? null,
            'month' => $this->data['month'] ?? null,
            'year' => $this->data['year'] ?? null,
            'total_hours' => $this->data['total_hours'] ?? null,
            'url' => $this->data['url'] ?? null,
            'level' => $this->data['level'] ?? 'success',
        ];
    }
}
