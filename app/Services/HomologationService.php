<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use Illuminate\Support\Facades\DB;

class HomologationService
{
    /**
     * Aprova um registro individual
     */
    public function approve(AttendanceSubmission $submission, int $userId): AttendanceSubmission
    {
        DB::transaction(function () use ($submission, $userId) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $submission->attendanceRecord()->update([
                'status' => AttendanceSubmission::STATUS_APPROVED,
                'approved_by_user_id' => $userId,
                'approved_at' => now(),
                'rejected_reason' => null,
                'rejected_at' => null,
            ]);
        });

        return $submission;
    }

    /**
     * Rejeita um registro individual
     */
    public function reject(AttendanceSubmission $submission, int $userId, string $reason): AttendanceSubmission
    {
        DB::transaction(function () use ($submission, $userId, $reason) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_REJECTED,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $submission->attendanceRecord()->update([
                'status' => AttendanceSubmission::STATUS_REJECTED,
                'approved_by_user_id' => $userId,
                'rejected_reason' => $reason,
                'rejected_at' => now(),
            ]);
        });

        return $submission;
    }

    public function markAsLate(AttendanceSubmission $submission, int $userId): AttendanceSubmission
    {
        DB::transaction(function () use ($submission, $userId) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_LATE,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);
        });

        return $submission;
    }
}
