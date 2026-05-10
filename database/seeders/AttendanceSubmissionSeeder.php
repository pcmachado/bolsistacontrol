<?php

namespace Database\Seeders;

use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Seeder;

class AttendanceSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $start = now()->copy()->startOfYear();
        $end = now()->copy()->startOfMonth();

        $holders = ScholarshipHolder::with('projects')->get();

        foreach ($holders as $holder) {
            foreach ($holder->projects as $project) {
                $cursor = $start->copy();

                while ($cursor <= $end) {
                    $isCurrentMonth = $cursor->isSameMonth(now());
                    $status = $isCurrentMonth
                        ? AttendanceSubmission::STATUS_DRAFT
                        : AttendanceSubmission::STATUS_APPROVED;

                    AttendanceSubmission::updateOrCreate(
                        [
                            'scholarship_holder_id' => $holder->id,
                            'project_id' => $project->id,
                            'year' => $cursor->year,
                            'month' => $cursor->month,
                        ],
                        [
                            'status' => $status,
                            'submitted_at' => $isCurrentMonth ? null : $cursor->copy()->endOfMonth(),
                            'approved_at' => $isCurrentMonth ? null : $cursor->copy()->endOfMonth()->addDays(2),
                            'rejected_at' => null,
                            'rejected_reason' => null,
                        ]
                    );

                    $cursor->addMonth();
                }
            }
        }
    }
}
