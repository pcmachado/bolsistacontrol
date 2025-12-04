<?php

namespace App\Services;

use Illuminate\Notifications\DatabaseNotification as Notification;
use App\Models\User;

class NotificationService
{
    public function sendToUser(User $user, string $message, string $type = 'info'): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'type'    => $type,
            'read'    => false,
        ]);
    }

    public function sendToMany($users, string $message, string $type = 'info')
    {
        foreach ($users as $user) {
            $this->sendToUser($user, $message, $type);
        }
    }
}
