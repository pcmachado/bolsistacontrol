<?php

namespace App\Policies;

use App\Models\AttendanceSubmission;
use App\Models\User;
use App\Services\VisibilityService;

class AttendanceSubmissionPolicy
{
    public function view(User $user, AttendanceSubmission $submission): bool
    {
        if ($user->scholarshipHolder?->id === $submission->scholarship_holder_id) {
            return true;
        }

        return $this->canReview($user, $submission);
    }

    public function submit(User $user, AttendanceSubmission $submission): bool
    {
        return $user->scholarshipHolder?->id === $submission->scholarship_holder_id
            && $submission->status === AttendanceSubmission::STATUS_DRAFT;
    }

    public function approve(User $user, AttendanceSubmission $submission): bool
    {
        return $submission->status === AttendanceSubmission::STATUS_SUBMITTED
            && $this->canReview($user, $submission);
    }

    public function reject(User $user, AttendanceSubmission $submission): bool
    {
        return $this->approve($user, $submission);
    }

    public function report(User $user, AttendanceSubmission $submission): bool
    {
        if ($user->scholarshipHolder?->id === $submission->scholarship_holder_id) {
            return true;
        }

        return $this->canReview($user, $submission);
    }

    protected function canReview(User $user, AttendanceSubmission $submission): bool
    {
        $canHomologateByRole = $user->hasAnyRole([
            'superadmin',
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ]);

        $canHomologateByPermission = $user->can('attendance.homologate.proreitoria');

        if (! $canHomologateByRole && ! $canHomologateByPermission) {
            return false;
        }

        if ($user->id === $submission->scholarshipHolder?->user_id) {
            return false;
        }

        if ($user->hasRole('coordenador_adjunto')) {
            $holderUser = $submission->scholarshipHolder?->user;

            if ($holderUser && ! $holderUser->hasRole('bolsista')) {
                return false;
            }
        }

        return app(VisibilityService::class)
            ->apply(AttendanceSubmission::query()->whereKey($submission->id), $user, 'admin')
            ->exists();
    }
}

