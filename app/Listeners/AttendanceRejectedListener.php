<?php

namespace App\Listeners;

use App\Events\AttendanceRejected;
use App\Notifications\RejectedAttendanceNotification;
use Illuminate\Support\Facades\Notification;

class AttendanceRejectedListener
{
    public function handle(AttendanceRejected $event)
    {
        $user = $event->record->scholarshipHolder->user;
        Notification::send($user, new RejectedAttendanceNotification($event->record));
    }
}
