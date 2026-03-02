<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\User;
use Illuminate\Support\Carbon;

class AttendanceDashboardService
{
    public function submissionCounts(User $user): array
    {
        $query = AttendanceSubmission::query();

        // 🔐 aplica visibilidade por papel
        app(AttendanceVisibilityService::class)
            ->apply($query, $user);

        return [
            'submitted'  => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
                ->count(),

            'approved' => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_APPROVED)
                ->count(),

            'rejected' => (clone $query)
                ->where('status', AttendanceSubmission::STATUS_REJECTED)
                ->count(),

            // opcional – atraso
            'late' => $this->lateSubmissionsCount($user),
        ];
    }

    /**
     * Submissões em atraso:
     * mês encerrado e nenhuma submissão criada
     */
    protected function lateSubmissionsCount(User $user): int
    {
        if (! $user->scholarshipHolder) {
            return 0;
        }

        $now = now()->subMonth(); // último mês fechado

        return AttendanceSubmission::query()
            ->where('scholarship_holder_id', $user->scholarshipHolder->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->whereNotIn('status', [
                AttendanceSubmission::STATUS_APPROVED,
                AttendanceSubmission::STATUS_SUBMITTED,
            ])
            ->count();
    }
}
