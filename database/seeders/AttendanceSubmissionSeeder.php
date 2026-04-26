<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
use App\Models\ProjectScholarshipHolder;
use Carbon\Carbon;

class AttendanceSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::create(2025, 11, 1);
        $end   = now()->startOfMonth();

        $holders = ScholarshipHolder::all();

        foreach ($holders as $holder) {

            $cursor = $start->copy();

            while ($cursor <= $end) {

                $monthsDiff = $cursor->diffInMonths(now());

                if ($cursor->isSameMonth(now())) {
                    $status = AttendanceSubmission::STATUS_DRAFT;
                } elseif ($monthsDiff === 1) {
                    $status = AttendanceSubmission::STATUS_SUBMITTED;
                } else {
                    $status = fake()->randomElement([
                        'approved',
                        'approved',
                        'approved',
                        'rejected',
                    ]);
                }

                AttendanceSubmission::updateOrCreate(
                    [
                        'scholarship_holder_id' => $holder->id,
                        'year'  => $cursor->year,
                        'month' => $cursor->month,
                    ],
                    [
                        'status'        => $status,
                        'submitted_at'  => in_array($status, ['submitted','approved','rejected'])
                            ? $cursor->copy()->endOfMonth()
                            : null,

                        'approved_at'   => $status === 'approved'
                            ? $cursor->copy()->endOfMonth()->addDays(2)
                            : null,

                        'rejected_at'   => $status === 'rejected'
                            ? now()->subDays(rand(1, 5)) // 🔥 importante p/ edição
                            : null,

                        'rejected_reason' => $status === 'rejected'
                            ? 'Ajustar horas inconsistentes.'
                            : null,
                    ]
                );

                $cursor->addMonth();
            }
        }

        AttendanceSubmission::all()->each(fn($s) => $s->recalculate());
    }
}