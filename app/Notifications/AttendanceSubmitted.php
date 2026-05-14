<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceSubmitted extends Notification
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
            'title' => $this->data['title'] ?? 'Nova frequência enviada',
            'message' => $this->data['message'] ?? 'Uma nova submissão de frequência foi enviada.',
            'submission_id' => $this->data['submission_id'] ?? null,
            'scholarship_holder_name' => $this->data['scholarship_holder_name'] ?? null,
            'month' => $this->data['month'] ?? null,
            'year' => $this->data['year'] ?? null,
            'total_hours' => $this->data['total_hours'] ?? null,
            'url' => $this->data['url'] ?? null,
            'level' => $this->data['level'] ?? 'info',
        ];
    }
}
