<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceApproved extends Notification
{
    use Queueable;

    public $attendance;

    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Registro homologado',
            'message' => 'Seu registro de frequência foi homologado com sucesso.',
            'attendance_id' => $this->attendance->id,
        ];
    }
}
