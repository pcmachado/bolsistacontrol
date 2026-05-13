<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\FinancialClosure;
use App\Models\ScholarshipHolder;
use Carbon\Carbon;
use DomainException;

class AttendanceRecordService
{
    public function create(ScholarshipHolder $holder, array $data): AttendanceRecord
    {
        $date = Carbon::parse($data['date']);
        $hours = $this->calculateHours($data['start_time'], $data['end_time']);
        $projectId = $this->resolveProjectId($holder, $data['project_id'] ?? null);

        if (! app(AttendanceSubmissionService::class)
            ->canCreateRecord($holder, $date->year, $date->month, $projectId)
        ) {
            throw new DomainException('Mês já fechado para edição.');
        }

        if (FinancialClosure::isClosed($holder->unit_id, $date->month, $date->year)) {
            throw new DomainException('Período financeiro fechado.');
        }

        app(AttendanceService::class)
            ->validateMonthlyLimit($holder, $date->year, $date->month, $hours, null, $projectId);

        return AttendanceRecord::create([
            'scholarship_holder_id' => $holder->id,
            'date' => $date,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'description' => $data['description'] ?? null,
            'hours' => $hours,
            'attendance_submission_id' => null,
            'project_id' => $projectId,
        ]);
    }

    public function update(AttendanceRecord $record, array $data): AttendanceRecord
    {
        if (! $record->isEditable()) {
            throw new DomainException('Este registro não pode ser alterado.');
        }

        if (FinancialClosure::isClosed(
            $record->scholarshipHolder->unit_id,
            $record->date->month,
            $record->date->year
        )) {
            throw new DomainException('Período financeiro fechado.');
        }

        $date = Carbon::parse($data['date']);
        $hours = $this->calculateHours($data['start_time'], $data['end_time']);
        $projectId = $this->resolveProjectId($record->scholarshipHolder, $data['project_id'] ?? null);

        app(AttendanceService::class)
            ->validateMonthlyLimit(
                $record->scholarshipHolder,
                $date->year,
                $date->month,
                $hours,
                $record->id,
                $projectId
            );

        $record->update([
            'date' => $date,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'description' => $data['description'] ?? null,
            'hours' => $hours,
            'project_id' => $projectId,
        ]);

        return $record;
    }

    public function deleteAttendance(AttendanceRecord $record): void
    {
        if (! $record->isEditable()) {
            throw new DomainException('Este registro não pode ser removido.');
        }

        if (FinancialClosure::isClosed(
            $record->scholarshipHolder->unit_id,
            $record->date->month,
            $record->date->year
        )) {
            throw new DomainException('Período financeiro fechado.');
        }

        $record->delete();
    }

    protected function calculateHours(string $start, string $end): float
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        return round($startTime->diffInMinutes($endTime) / 60, 2);
    }

    protected function resolveProjectId(ScholarshipHolder $holder, mixed $projectId): int
    {
        if (! $projectId) {
            throw new DomainException('Selecione um projeto.');
        }

        $project = $holder->projects()
            ->where('projects.id', $projectId)
            ->first();

        if (! $project) {
            abort(403, 'Projeto inválido para este bolsista.');
        }

        return (int) $project->id;
    }
}
