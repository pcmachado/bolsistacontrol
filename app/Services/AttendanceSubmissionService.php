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
            throw new \InvalidArgumentException('Formato de mês inválido.');
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
     * Vincula automaticamente os registros do mês à submissão
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
     * Remove um registro da submissão (antes do envio)
     */
    public function removeRecord(AttendanceSubmission $submission, AttendanceRecord $record): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_DRAFT) {
            throw new \DomainException('Submissão já enviada.');
        }

        if ($record->attendance_submission_id !== $submission->id) {
            throw new \DomainException('Registro não pertence a esta submissão.');
        }

        $record->update([
            'attendance_submission_id' => null,
        ]);
    }

    /**
     * Envia a submissão para homologação
     */
    public function submit(AttendanceSubmission $submission): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_DRAFT) {
            throw new \DomainException('A submissão não está em rascunho.');
        }

        $this->attachMonthlyRecords($submission);

        if ($submission->attendanceRecords()->count() === 0) {
            throw new \DomainException('Não é possível enviar uma submissão vazia.');
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
                'title' => 'Nova Submissão de Frequência',
                'message' => "O bolsista {$submission->scholarshipHolder->user->name} enviou uma submissão de frequência para {$submission->month}/{$submission->year}",
                'level' => 'info',
                'submission_id' => $submission->id,
                'url' => route('attendance.submissions.show', $submission),
                'scholarship_holder_name' => $submission->scholarshipHolder->user->name,
                'month' => $submission->month,
                'year' => $submission->year,
                'total_hours' => $submission->total_hours,
                'unit_id' => $submission->scholarshipHolder->unit_id,
                'submitter_user_id' => $submission->scholarshipHolder->user_id,
                'scholarship_holder_user_id' => $submission->scholarshipHolder->user_id,
            ],
            $submission->project_id,
            $submission->scholarshipHolder->unit->institution_id ?? null
        );
    }

    /**
     * Aprovação da submissão (coordenador)
     */
    public function approve(AttendanceSubmission $submission, int $userId): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_SUBMITTED) {
            throw new \DomainException('Submissão não está pendente.');
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
                'title' => 'Submissão de Frequência Aprovada',
                'message' => "Sua submissão de frequência para {$submission->month}/{$submission->year} foi aprovada",
                'level' => 'success',
                'submission_id' => $submission->id,
                'url' => route('my-attendance.submissions.show', $submission),
                'month' => $submission->month,
                'year' => $submission->year,
                'total_hours' => $submission->total_hours,
                'unit_id' => $submission->scholarshipHolder->unit_id,
                'submitter_user_id' => $submission->scholarshipHolder->user_id,
                'scholarship_holder_user_id' => $submission->scholarshipHolder->user_id,
            ],
            $submission->project_id,
            $submission->scholarshipHolder->unit->institution_id ?? null
        );
    }

    /**
     * Rejeição da submissão (coordenador)
     */
    public function reject(AttendanceSubmission $submission, string $reason, int $userId): void
    {
        if ($submission->status !== AttendanceSubmission::STATUS_SUBMITTED) {
            throw new \DomainException('Submissão não está pendente.');
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
                'title' => 'Submissão de Frequência Rejeitada',
                'message' => "Sua submissão de frequência para {$submission->month}/{$submission->year} foi rejeitada. Motivo: {$reason}",
                'level' => 'danger',
                'submission_id' => $submission->id,
                'url' => route('my-attendance.submissions.show', $submission),
                'month' => $submission->month,
                'year' => $submission->year,
                'reason' => $reason,
                'unit_id' => $submission->scholarshipHolder->unit_id,
                'submitter_user_id' => $submission->scholarshipHolder->user_id,
                'scholarship_holder_user_id' => $submission->scholarshipHolder->user_id,
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

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (! $submission) {
            return AttendanceSubmission::create([
                'scholarship_holder_id' => $holder->id,
                'project_id' => $projectId,
                'year' => $year,
                'month' => $month,
                'status' => AttendanceSubmission::STATUS_DRAFT,
            ]);
        }

        if ($submission->status === AttendanceSubmission::STATUS_REJECTED) {
            $submission->update([
                'status' => AttendanceSubmission::STATUS_DRAFT,
                'submitted_at' => null,
                'rejected_at' => null,
                'rejected_reason' => null,
                'approved_by' => null,
            ]);
        }

        return $submission;
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
                abort(403, 'Projeto inválido para este bolsista.');
            }

            throw new \DomainException('Nenhum projeto válido vinculado ao bolsista.');
        }

        return (int) $project->id;
    }
}
