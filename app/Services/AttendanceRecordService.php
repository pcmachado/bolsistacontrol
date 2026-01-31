<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\ScholarshipHolder;
use Carbon\Carbon;
use DomainException;

class AttendanceRecordService
{
    /**
     * Criação de registro
     */
    public function create(
        ScholarshipHolder $holder,
        array $data
    ): AttendanceRecord {
        return AttendanceRecord::create([
            'scholarship_holder_id'       => $holder->id,
            'date'                        => Carbon::parse($data['date']),
            'start_time'                  => $data['start_time'],
            'end_time'                    => $data['end_time'],
            'description'                 => $data['description'] ?? null,
            'hours'                       => $this->calculateHours(
                $data['start_time'],
                $data['end_time']
            ),
            'attendance_submission_id'    => null,
        ]);
    }

    /**
     * Atualização
     */
    public function update(
        AttendanceRecord $record,
        array $data
    ): AttendanceRecord {
        if (! $this->isEditable($record)) {
            throw new DomainException(
                'Este registro não pode ser alterado.'
            );
        }

        $record->update([
            'date'        => Carbon::parse($data['date']),
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'description' => $data['description'] ?? null,
            'hours'       => $this->calculateHours(
                $data['start_time'],
                $data['end_time']
            ),
        ]);

        return $record;
    }

    /**
     * Exclusão
     */
    public function delete(AttendanceRecord $record): void
    {
        if (! $this->isEditable($record)) {
            throw new DomainException(
                'Este registro não pode ser removido.'
            );
        }

        $record->delete();
    }

    /**
     * Regra central de edição
     */
    public function isEditable(AttendanceRecord $record): bool
    {
        if (! $record->attendance_submission_id) {
            return true;
        }

        return $record->submission?->status ===
            AttendanceSubmission::STATUS_DRAFT;
    }

    protected function calculateHours(string $start, string $end): float
    {
        $startTime = Carbon::parse($start);
        $endTime   = Carbon::parse($end);

        return round($startTime->diffInMinutes($endTime) / 60, 2);
    }
}
