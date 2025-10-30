<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;

class HomologationService
{
    /**
     * Aprova um registro individual
     */
    public function approve(AttendanceRecord $record, int $userId): AttendanceRecord
    {
        $record->update([
            'status' => AttendanceRecord::STATUS_APPROVED,
            'approved_by_user_id' => $userId,
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        return $record;
    }

    /**
     * Rejeita um registro individual
     */
    public function reject(AttendanceRecord $record, int $userId, string $reason): AttendanceRecord
    {
        $record->update([
            'status' => AttendanceRecord::STATUS_REJECTED,
            'approved_by_user_id' => $userId, // ou rejected_by_user_id se quiser separar
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        return $record;
    }

    /**
     * Processa registros em lote (aprovação ou rejeição)
     */
    public function bulk(Collection $records, string $action, int $userId, ?string $reason = null): void
    {
        foreach ($records as $record) {
            if ($action === 'approve') {
                $this->approve($record, $userId);
            } elseif ($action === 'reject') {
                $this->reject($record, $userId, $reason ?? 'Sem motivo informado');
            }
        }
    }
}
