<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\PendingShipment;
use Illuminate\Support\Facades\Notification;

class AttendanceService
{
    public function calculateDuration($start, $end)
    {
        return Carbon::parse($end)->diffInMinutes(Carbon::parse($start));
    }

    public function validateWeeklyLimit(ScholarshipHolder $scholarshipHolder, Carbon $date, int $newDuration)
    {
        $startOfWeek = $date->startOfWeek();
        $endOfWeek = $date->endOfWeek();

        $totalWeek = AttendanceRecord::where('scholarship_holder_id', $scholarshipHolder->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)'));

        return ($totalWeek + $newDuration) <= $scholarshipHolder->weekly_limit_minutes;
    }

    public function generatePendingShipments()
    {
        $dueDate = Carbon::now()->subMonth()->startOfMonth()->addDays(5);

        ScholarshipHolder::whereDoesntHave('attendances', function ($query) use ($dueDate) {
            $query->where('status', 'sent')->where('date', '<=', $dueDate);
        })->each(function ($scholarshipHolder) {
            Notification::send($scholarshipHolder->coordinator, new PendingShipment($scholarshipHolder));
        });
    }
}
