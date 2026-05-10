<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceSubmissionService
{
    public function createFromMonth(User $user, string $month, ?int $projectId = null): AttendanceSubmission
    {
        $holder = $user->scholarshipHolder;

        if (! $holder) {
            abort(403);
        }

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            throw new \InvalidArgumentException('Formato de mÃªs invÃ¡lido.');
        }

        [$year, $month] = explode('-', $month);

        $submission = $this->getOrCreateDraft(
            $holder,
            (int) $year,
            (int) $month,
            $projectId
        );

        $this->attachMonthlyRecords($submission);

        return $submission;
    }

    /**
     * Vincula automaticamente os registros do mÃªs Ã  submissÃ£o
     */
    public function attachMonthlyRecords(AttendanceSubmission $submission): void
    {
        AttendanceRecord::query()
            ->where('scholarship_holder_id', $submission->scholarship_holder_id)
            ->where('project_id', $submission->project_id)
            ->whereYear('date', $submission->year)
            ->whereMonth('date', $submission->month)
            ->update([
                'attendance_submission_id' => $submission->id,
            ]);
    }

    /**
     * Remove um registro da submissÃ£o (antes do envio)
     */
    public function removeRecord(AttendanceSubmission $submission, AttendanceRecord $record): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_DRAFT) {
            throw new \DomainException('SubmissÃ£o jÃ¡ enviada.');
        }

        if ($record->attendance_submission_id !== $submission->id) {
            throw new \DomainException('Registro nÃ£o pertence a esta submissÃ£o.');
        }

        $record->update([
            'attendance_submission_id' => null,
        ]);
    }

    /**
     * Envia a submissÃ£o para homologaÃ§Ã£o
     */
    public function submit(AttendanceSubmission $submission): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_DRAFT) {
            throw new \DomainException('A submissÃ£o nÃ£o estÃ¡ em rascunho.');
        }

        $this->attachMonthlyRecords($submission);

        if ($submission->attendanceRecords()->count() === 0) {
            throw new \DomainException('NÃ£o Ã© possÃ­vel enviar uma submissÃ£o vazia.');
        }

        $submission->recalculate();

        DB::transaction(function () use ($submission) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'rejected_at' => null,
                'rejected_reason' => null,
            ]);
        });

        $notificationService = app(NotificationService::class);
        $notificationService->sendEventNotification(
            'submission_submitted',
            [
                'title' => 'Nova SubmissÃ£o de FrequÃªncia',
                'message' => "O bolsista {$submission->scholarshipHolder->user->name} enviou uma submissÃ£o de frequÃªncia para {$submission->month}/{$submission->year}",
                'level' => 'info',
                'submission_id' => $submission->id,
                'url' => route('attendance.submissions.show', $submission),
                'scholarship_holder_name' => $submission->scholarshipHolder->user->name,
                'month' => $submission->month,
                'year' => $submission->year,
                'total_hours' => $submission->total_hours,
            ],
            $submission->project_id,
            $submission->scholarshipHolder->unit->institution_id ?? null
        );
    }

    /**
     * AprovaÃ§Ã£o da submissÃ£o (coordenador)
     */
    public function approve(AttendanceSubmission $submission, int $userId): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_SUBMITTED) {
            throw new \DomainException('SubmissÃ£o nÃ£o estÃ¡ pendente.');
        }

        $submission->update([
            'status' => AttendanceSubmission::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);

        app(PaymentGenerationService::class)->generateFromSubmission(
            $submission->fresh(['project', 'scholarshipHolder'])
        );

        $notificationService = app(NotificationService::class);
        $notificationService->sendEventNotification(
            'submission_approved',
            [
                'title' => 'SubmissÃ£o de FrequÃªncia Aprovada',
                'message' => "Sua submissÃ£o de frequÃªncia para {$submission->month}/{$submission->year} foi aprovada",
                'level' => 'success',
                'submission_id' => $submission->id,
                'url' => route('my-attendance.submissions.show', $submission),
                'month' => $submission->month,
                'year' => $submission->year,
                'total_hours' => $submission->total_hours,
            ],
            $submission->project_id,
            $submission->scholarshipHolder->unit->institution_id ?? null
        );
    }

    /**
     * RejeiÃ§Ã£o da submissÃ£o (coordenador)
     */
    public function reject(AttendanceSubmission $submission, string $reason, int $userId): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_SUBMITTED) {
            throw new \DomainException('SubmissÃ£o nÃ£o estÃ¡ pendente.');
        }

        DB::transaction(function () use ($submission, $reason, $userId) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_REJECTED,
                'rejected_reason' => $reason,
                'rejected_at' => now(),
                'approved_by' => $userId,
            ]);

            $submission->attendanceRecords()->update([
                'attendance_submission_id' => null,
            ]);
        });

        $notificationService = app(NotificationService::class);
        $notificationService->sendEventNotification(
            'submission_rejected',
            [
                'title' => 'SubmissÃ£o de FrequÃªncia Rejeitada',
                'message' => "Sua submissÃ£o de frequÃªncia para {$submission->month}/{$submission->year} foi rejeitada. Motivo: {$reason}",
                'level' => 'danger',
                'submission_id' => $submission->id,
                'url' => route('my-attendance.submissions.show', $submission),
                'month' => $submission->month,
                'year' => $submission->year,
                'reason' => $reason,
            ],
            $submission->project_id,
            $submission->scholarshipHolder->unit->institution_id ?? null
        );
    }

    public function canCreateRecord(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        ?int $projectId = null
    ): bool {
        return ! AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->where('year', $year)
            ->where('month', $month)
            ->whereIn('status', [
                AttendanceSubmission::STATUS_SUBMITTED,
                AttendanceSubmission::STATUS_APPROVED,
            ])
            ->exists();
    }

    protected function getOrCreateDraft(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        ?int $projectId = null
    ): AttendanceSubmission {
        $projectId = $this->resolveProjectId($holder, $projectId);

        return AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->firstOr(function () use ($holder, $year, $month, $projectId) {
                return AttendanceSubmission::create([
                    'scholarship_holder_id' => $holder->id,
                    'project_id' => $projectId,
                    'year' => $year,
                    'month' => $month,
                    'status' => AttendanceSubmission::STATUS_DRAFT,
                ]);
            });
    }

    public function findById($id): AttendanceSubmission
    {
        return AttendanceSubmission::query()
            ->with(['project', 'scholarshipHolder.user', 'scholarshipHolder.unit'])
            ->findOrFail($id);
    }

    public function isClosed(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        ?int $projectId = null
    ): bool {
        return AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->where('year', $year)
            ->where('month', $month)
            ->whereIn('status', [
                AttendanceSubmission::STATUS_SUBMITTED,
                AttendanceSubmission::STATUS_APPROVED,
            ])
            ->exists();
    }

    protected function resolveProjectId(ScholarshipHolder $holder, ?int $projectId = null): int
    {
        $project = $projectId
            ? $holder->projects()->where('projects.id', $projectId)->first()
            : $holder->projects()->first();

        if (! $project) {
            if ($projectId !== null) {
                abort(403, 'Projeto invÃ¡lido para este bolsista.');
            }

            throw new \DomainException('Nenhum projeto vÃ¡lido vinculado ao bolsista.');
        }

        return (int) $project->id;
    }
}
