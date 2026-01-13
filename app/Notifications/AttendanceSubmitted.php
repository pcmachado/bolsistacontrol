<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceSubmitted extends Notification
{
    use Queueable;

    protected $attendance;

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
            'title' => 'Nova frequência enviada',
            'message' => "O bolsista {$this->attendance->scholarshipHolder->user->name} enviou uma frequência.",
            'attendance_id' => $this->attendance->id,
        ];
    }
}
