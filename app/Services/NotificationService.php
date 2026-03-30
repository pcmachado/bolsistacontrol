<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;

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
}