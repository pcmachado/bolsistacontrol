<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use Carbon\Carbon;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = AttendanceSubmission::with('scholarshipHolder')->get();

        foreach ($submissions as $submission) {

            $monthStart = Carbon::create($submission->year, $submission->month, 1);
            $monthEnd   = $monthStart->copy()->endOfMonth();

            $days = collect();
            $cursor = $monthStart->copy();

            while ($cursor <= $monthEnd) {
                if ($cursor->isWeekday()) {
                    $days->push($cursor->copy());
                }
                $cursor->addDay();
            }

            $selectedDays = $days->random(min($days->count(), rand(8, 15)));

            foreach ($selectedDays as $date) {

                $startHour = rand(8, 13);
                $hours     = rand(2, 6);

                // 🔥 REGRA PRINCIPAL
                $submissionId = match ($submission->status) {

                    // EDITÁVEIS → alguns ficam soltos
                    AttendanceSubmission::STATUS_DRAFT, AttendanceSubmission::STATUS_REJECTED => rand(0,1)
                        ? null
                        : $submission->id,

                    // BLOQUEADOS → sempre vinculados
                    AttendanceSubmission::STATUS_SUBMITTED, AttendanceSubmission::STATUS_APPROVED => $submission->id,

                    default => null,
                };

                AttendanceRecord::create([
                    'scholarship_holder_id'    => $submission->scholarship_holder_id,
                    'attendance_submission_id' => $submissionId,
                    'date'        => $date,
                    'start_time'  => sprintf('%02d:00', $startHour),
                    'end_time'    => sprintf('%02d:00', $startHour + $hours),
                    'hours'       => $hours,
                    'description' => 'Atividades do projeto no dia',
                ]);
            }
        }
    }
}