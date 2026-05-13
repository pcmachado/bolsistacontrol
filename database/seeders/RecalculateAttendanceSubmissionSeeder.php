<?php

namespace Database\Seeders;

use App\Models\AttendanceSubmission;
use Illuminate\Database\Seeder;

class RecalculateAttendanceSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceSubmission::with(['attendanceRecords', 'project.positions', 'scholarshipHolder'])->get()
            ->each(function (AttendanceSubmission $submission) {
                $hours = $submission->attendanceRecords->sum('hours');

                if ($hours == 0) {
                    return;
                }

                $holder = $submission->scholarshipHolder;
                $project = $submission->project;
                $rate = $project?->hourlyRateForScholarshipHolder($holder) ?? 0;

                $submission->update([
                    'total_hours' => $hours,
                    'calculated_value' => $hours * $rate,
                ]);
            });
    }
}
