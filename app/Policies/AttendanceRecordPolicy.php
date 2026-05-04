<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\User;
use App\Services\VisibilityService;

class AttendanceRecordPolicy
{
    public function view(User $user, AttendanceRecord $record): bool
    {
        if ($user->scholarshipHolder?->id === $record->scholarship_holder_id) {
            return true;
        }

        if (! $user->hasAnyRole([
            'superadmin',
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ])) {
            return false;
        }

        return app(VisibilityService::class)
            ->apply(AttendanceRecord::query()->whereKey($record->id), $user, 'admin')
            ->exists();
    }

    public function update(User $user, AttendanceRecord $record): bool
    {
        if ($user->scholarshipHolder?->id !== $record->scholarship_holder_id) {
            return false;
        }

        if ($record->attendance_submission_id === null) {
            return true;
        }

        $submission = $record->submission;

        if (! $submission) {
            return false;
        }

        return $submission->status === AttendanceSubmission::STATUS_REJECTED;
    }

    public function deleteAttendanceRecord(User $user, AttendanceRecord $record): bool
    {
        return $this->update($user, $record);
    }
}
