<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceRejected extends Notification
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

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Registro rejeitado',
            'message' => 'Seu registro de frequência foi rejeitado. Motivo: ' . $this->attendance->rejected_reason,
            'attendance_id' => $this->attendance->id,
            'date' => $this->attendance->date->format('Y-m-d'),
            'url' => route('attendance.index', ['month' => $this->attendance->date->format('Y-m')]),
            'level' => 'error',
        ];
    }
}
