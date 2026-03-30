<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
use Carbon\Carbon;
use DomainException;

class AttendanceRecordService
{
    public function create(ScholarshipHolder $holder, array $data): AttendanceRecord
    {
        $date  = Carbon::parse($data['date']);
        $hours = $this->calculateHours($data['start_time'], $data['end_time']);

        // 🔒 valida se pode registrar nesse mês
        if (! app(AttendanceSubmissionService::class)
            ->canCreateRecord($holder, $date->year, $date->month)
        ) {
            throw new DomainException('Mês já fechado para edição.');
        }

        // 🔥 valida limite mensal
        app(AttendanceService::class)
            ->validateMonthlyLimit($holder, $date->year, $date->month, $hours);

        return AttendanceRecord::create([
            'scholarship_holder_id'    => $holder->id,
            'date'                     => $date,
            'start_time'               => $data['start_time'],
            'end_time'                 => $data['end_time'],
            'description'              => $data['description'] ?? null,
            'hours'                    => $hours,
            'attendance_submission_id' => null,
        ]);
    }

    public function update(AttendanceRecord $record, array $data): AttendanceRecord
    {
        if (! $record->isEditable()) {
            throw new DomainException('Este registro não pode ser alterado.');
        }

        $date  = Carbon::parse($data['date']);
        $hours = $this->calculateHours($data['start_time'], $data['end_time']);

        app(AttendanceService::class)
            ->validateMonthlyLimit(
                $record->scholarshipHolder,
                $date->year,
                $date->month,
                $hours,
                $record->id // ignora o próprio registro
            );

        $record->update([
            'date'        => $date,
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'description' => $data['description'] ?? null,
            'hours'       => $hours,
        ]);

        return $record;
    }

    public function delete(AttendanceRecord $record): void
    {
        if (! $record->isEditable()) {
            throw new DomainException('Este registro não pode ser removido.');
        }

        $record->delete();
    }

    protected function calculateHours(string $start, string $end): float
    {
        $startTime = Carbon::parse($start);
        $endTime   = Carbon::parse($end);

        return round($startTime->diffInMinutes($endTime) / 60, 2);
    }
}