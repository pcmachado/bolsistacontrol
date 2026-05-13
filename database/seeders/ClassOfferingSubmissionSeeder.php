<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class ClassOfferingSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = ClassOffering::with(['students', 'monthRecords'])->get();

        foreach ($offerings as $offering) {
            if (! $offering->start_date || ! $offering->end_date) {
                continue;
            }

            $startDate = Carbon::parse($offering->start_date);
            $endDate = Carbon::parse($offering->end_date);

            if ($endDate->lessThan($startDate)) {
                continue;
            }

            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            foreach ($period as $date) {
                $isCurrentMonth = $date->isSameMonth(now());
                $monthRecords = $offering->monthRecords
                    ->where('month', $date->month)
                    ->where('year', $date->year);
                $totalStudents = $monthRecords->count() ?: $offering->students->count();
                $dailyRate = $offering->project?->student_daily_rate ?? 0;
                $totalAmount = $monthRecords->sum('attended_classes') * $dailyRate;

                ClassOfferingSubmission::updateOrCreate(
                    [
                        'class_offering_id' => $offering->id,
                        'month' => $date->month,
                        'year' => $date->year,
                    ],
                    [
                        'total_students' => $totalStudents,
                        'total_amount' => $totalAmount,
                        'status' => $isCurrentMonth ? 'draft' : 'approved',
                        'submitted_at' => $isCurrentMonth ? null : $date->copy()->endOfMonth(),
                        'approved_at' => $isCurrentMonth ? null : $date->copy()->endOfMonth()->addDays(2),
                        'rejected_at' => null,
                        'rejected_reason' => null,
                    ]
                );
            }
        }
    }
}
