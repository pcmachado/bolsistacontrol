<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSubmissionService
{
    public function createFromMonth(User $user, string $month): AttendanceSubmission
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
            (int) $month
        );

        $this->attachMonthlyRecords($submission);

        return $submission;
    }

    /**
     * Vincula automaticamente os registros do mês à submissão
     */
    public function attachMonthlyRecords(AttendanceSubmission $submission): void
    {
        $records = AttendanceRecord::query()
            ->where('scholarship_holder_id', $submission->scholarship_holder_id)
            ->whereYear('date', $submission->year)
            ->whereMonth('date', $submission->month)
            ->whereNull('attendance_submission_id')
            ->get();

        foreach ($records as $record) {
            $record->update([
                'attendance_submission_id' => $submission->id,
            ]);
        }
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

        if ($submission->records()->count() === 0) {
            throw new \DomainException('Não é possível enviar uma submissão vazia.');
        }

        DB::transaction(function () use ($submission) {
            $submission->update([
                'status'       => AttendanceSubmission::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);
        });
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
            'status'        => AttendanceSubmission::STATUS_APPROVED,
            'approved_at'   => now(),
            'approved_by'   => $userId,
        ]);
    }

    /**
     * Rejeição da submissão (coordenador)
     */
    public function reject(
        AttendanceSubmission $submission,
        string $reason,
        int $userId
    ): void {
        if ($submission->status !== AttendanceSubmission::STATUS_SUBMITTED) {
            throw new \DomainException('Submissão não está pendente.');
        }

        DB::transaction(function () use ($submission, $reason, $userId) {
            $submission->update([
                'status'          => AttendanceSubmission::STATUS_REJECTED,
                'rejected_reason' => $reason,
                'rejected_at'     => now(),
                'approved_by'     => $userId,
            ]);

            // devolve os registros para edição
            $submission->records()->update([
                'attendance_submission_id' => null,
            ]);
        });
    }

    public function canCreateRecord(ScholarshipHolder $holder, int $year, int $month): bool 
    {
        return ! AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $year)
            ->where('month', $month)
            ->whereIn('status', [
                AttendanceSubmission::STATUS_SUBMITTED,
                AttendanceSubmission::STATUS_APPROVED,
            ])
            ->exists();
    }
}
