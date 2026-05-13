<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = AttendanceSubmission::with('scholarshipHolder')->get();

        foreach ($submissions as $submission) {
            $monthStart = Carbon::create($submission->year, $submission->month, 1);
            $monthEnd = $monthStart->isSameMonth(now())
                ? now()->copy()->endOfDay()
                : $monthStart->copy()->endOfMonth();

            $days = collect();
            $cursor = $monthStart->copy();

            while ($cursor <= $monthEnd) {
                if ($cursor->isWeekday()) {
                    $days->push($cursor->copy());
                }
                $cursor->addDay();
            }

            if ($days->isEmpty()) {
                continue;
            }

            $selectedDays = $days->random(min($days->count(), rand(8, 15)));

            foreach ($selectedDays as $date) {
                $startHour = rand(8, 13);
                $hours = rand(2, 6);

                $isOpen = $submission->status === AttendanceSubmission::STATUS_DRAFT;

                AttendanceRecord::updateOrCreate(
                    [
                        'scholarship_holder_id' => $submission->scholarship_holder_id,
                        'project_id' => $submission->project_id,
                        'date' => $date->toDateString(),
                        'start_time' => sprintf('%02d:00', $startHour),
                    ],
                    [
                        'attendance_submission_id' => $isOpen && rand(0, 1) ? null : $submission->id,
                        'end_time' => sprintf('%02d:00', $startHour + $hours),
                        'hours' => $hours,
                        'description' => 'Atividades do projeto no dia',
                    ]
                );
            }
        }
    }
}
