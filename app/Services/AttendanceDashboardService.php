<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\User;

class AttendanceDashboardService
{
    public function submissionCounts(User $user, string $context = 'admin'): array
    {
        $query = app(VisibilityService::class)
            ->apply(AttendanceSubmission::query(), $user, $context);

        return [
            'submitted' => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
                ->count(),
            'approved' => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_APPROVED)
                ->count(),
            'rejected' => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_REJECTED)
                ->count(),
            'late' => $this->lateSubmissionsCount($user, $context),
        ];
    }

    protected function lateSubmissionsCount(User $user, string $context = 'self'): int
    {
        $now = now()->subMonth();

        $query = AttendanceSubmission::query()
            ->where('year', $now->year)
            ->where('month', $now->month);

        $query = app(VisibilityService::class)->apply($query, $user, $context);

        return $query->whereNotIn('status', [
            AttendanceSubmission::STATUS_APPROVED,
            AttendanceSubmission::STATUS_SUBMITTED,
        ])->count();
    }
}
