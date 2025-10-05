<?php

namespace App\Events;

use App\Models\AttendanceRecord;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Notification;

class AttendanceApproved implements ShouldBroadcast
{
    use SerializesModels;

    public AttendanceRecord $record;

    public function __construct(AttendanceRecord $record)
    {
        $this->record = $record;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->record->scholarshipHolder->user_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Seu registro de frequência foi homologado.',
            'record_id' => $this->record->id,
        ];
    }
}
