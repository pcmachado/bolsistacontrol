<?php

namespace App\Listeners;

use App\Events\AttendanceApproved;
use App\Notifications\AttendanceApprovedNotification;
use Illuminate\Support\Facades\Notification;

class AttendanceApprovedListener
{
    public function handle(AttendanceApproved $event)
    {
        $user = $event->record->scholarshipHolder->user;
        Notification::send($user, new AttendanceApprovedNotification($event->record));
    }
}
