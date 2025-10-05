<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceApprovedNotification extends Notification
{
    use Queueable;

    public AttendanceRecord $record;

    public function __construct(AttendanceRecord $record)
    {
        $this->record = $record;
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
            'record_id' => $this->record->id,
        ];
    }
}
