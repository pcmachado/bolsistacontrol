<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
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

                // status por regra simples
                if ($cursor->isSameMonth(now())) {
                    $status = AttendanceSubmission::STATUS_DRAFT;
                } elseif ($cursor->diffInMonths(now()) === 1) {
                    $status = AttendanceSubmission::STATUS_SUBMITTED;
                } else {
                    $status = collect([
                        AttendanceSubmission::STATUS_APPROVED,
                        AttendanceSubmission::STATUS_REJECTED,
                    ])->random();
                }

                AttendanceSubmission::firstOrCreate(
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
                            ? $cursor->copy()->endOfMonth()->addDays(2)
                            : null,
                        'rejected_reason' => $status === 'rejected'
                            ? 'Inconsistência nas horas lançadas'
                            : null,
                    ]
                );

                $cursor->addMonth();
            }
        }
    }
}
