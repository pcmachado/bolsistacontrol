<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceSubmission;

class RecalculateAttendanceSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceSubmission::with('attendanceRecords')->get()
            ->each(function ($submission) {

                $hours = $submission->attendanceRecords->sum('hours');

                if ($hours == 0) return;

                $holder = $submission->scholarshipHolder;
                $project = $holder?->projects->first();

                $positionId = $project?->pivot->position_id;

                $rate = $project?->positions
                    ->firstWhere('id', $positionId)
                    ?->pivot->hourly_rate ?? 0;

                $submission->update([
                    'total_hours' => $hours,
                    'calculated_value' => $hours * $rate,
                ]);
            });
    }
}
