<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\User;

class AttendanceDashboardService
{
    public function submissionCounts(
        User $user,
        ?int $projectId = null,
        string $context = 'admin'
    ): array {

        $query = app(VisibilityService::class)
            ->apply(AttendanceSubmission::query(), $user, $context)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

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

            'late' => $this->lateSubmissionsCount($user, $projectId, $context),
        ];
    }

    protected function lateSubmissionsCount(
        User $user,
        ?int $projectId = null,
        string $context = 'self'
    ): int {

        $now = now()->subMonth();

        $query = AttendanceSubmission::query()
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

        $query = app(VisibilityService::class)->apply($query, $user, $context);

        return $query->whereNotIn('status', [
            AttendanceSubmission::STATUS_APPROVED,
            AttendanceSubmission::STATUS_SUBMITTED,
        ])->count();
    }
}
