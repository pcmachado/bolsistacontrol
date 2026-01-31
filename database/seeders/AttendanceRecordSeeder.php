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

            $monthStart = Carbon::create(
                $submission->year,
                $submission->month,
                1
            );

            $monthEnd = $monthStart->copy()->endOfMonth();

            $days = collect();
            $cursor = $monthStart->copy();

            while ($cursor <= $monthEnd) {
                if ($cursor->isWeekday()) {
                    $days->push($cursor->copy());
                }
                $cursor->addDay();
            }

            // cria de 8 a 15 registros no mês
            $days->random(rand(8, 15))->each(function ($date) use ($submission) {

                $startHour = rand(8, 14);
                $hours     = rand(2, 6);

                AttendanceRecord::create([
                    'scholarship_holder_id'      => $submission->scholarship_holder_id,
                    'attendance_submission_id'   => $submission->id,
                    'date'        => $date,
                    'start_time'  => sprintf('%02d:00', $startHour),
                    'end_time'    => sprintf('%02d:00', $startHour + $hours),
                    'hours'       => $hours,
                    'description' => 'Atividades do projeto no dia',
                    'status'      => 'draft', // status agora vive na submission
                ]);
            });
        }
    }
}
