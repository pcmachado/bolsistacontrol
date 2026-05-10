<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\StudentMonthRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class StudentMonthRecordSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = ClassOffering::with('students')->get();

        foreach ($offerings as $offering) {
            if (! $offering->start_date || ! $offering->end_date) {
                continue;
            }

            $startDate = Carbon::parse($offering->start_date);
            $endDate = Carbon::parse($offering->end_date);
            $currentMonthEnd = now()->copy()->endOfMonth();

            $period = CarbonPeriod::create(
                $startDate,
                '1 month',
                $endDate->lessThan($currentMonthEnd) ? $endDate : $currentMonthEnd
            );

            foreach ($period as $date) {
                foreach ($offering->students as $student) {
                    $absences = rand(0, 5);
                    $attendedClasses = rand(10, 20);

                    StudentMonthRecord::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_offering_id' => $offering->id,
                            'month' => $date->month,
                            'year' => $date->year,
                        ],
                        [
                            'absences' => $absences,
                            'attended_classes' => $attendedClasses,
                        ]
                    );
                }
            }
        }
    }
}
