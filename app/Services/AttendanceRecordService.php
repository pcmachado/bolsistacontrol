<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\PendingShipment;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordService
{
    public function calculateDuration($start, $end)
    {
        return Carbon::parse($end)->diffInMinutes(Carbon::parse($start));
    }

    public function validateWeeklyLimit(ScholarshipHolder $scholarshipHolder, Carbon $date, int $newDuration)
    {
        $startOfWeek = $date->startOfWeek();
        $endOfWeek = $date->endOfWeek();

        $totalWeek = AttendanceRecord::where('scholarship_holder_id', $scholarshipHolder->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)'));

        return ($totalWeek + $newDuration) <= $scholarshipHolder->weekly_limit_minutes;
    }

    public function generatePendingShipments()
    {
        $dueDate = Carbon::now()->subMonth()->startOfMonth()->addDays(5);

        ScholarshipHolder::whereDoesntHave('attendances', function ($query) use ($dueDate) {
            $query->where('status', 'sent')->where('date', '<=', $dueDate);
        })->each(function ($scholarshipHolder) {
            Notification::send($scholarshipHolder->coordinator, new PendingShipment($scholarshipHolder));
        });
    }

    /**
     * Bolsista envia registro para homologação
     */
    public function submitRecord(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $now = Carbon::now();

        // Verifica prazo (até dia 5 do mês seguinte)
        $limitDate = Carbon::parse($attendanceRecord->date)->endOfMonth()->addDays(5);
        if ($now->greaterThan($limitDate)) {
            throw new \Exception("Prazo de envio expirado. Registros só podem ser enviados até o dia 5 do mês seguinte.");
        }

        if ($attendanceRecord->status !== 'draft') {
            throw new \Exception("Somente registros em rascunho podem ser enviados.");
        }

        $attendanceRecord->update([
            'status' => 'submitted',
            'submitted_at' => $now,
        ]);

        return $attendanceRecord;
    }

    /**
     * Coordenador aprova registro
     */
    public function approveRecord(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $now = Carbon::now();

        // Verifica prazo (até dia 10 do mês seguinte)
        $limitDate = Carbon::parse($attendanceRecord->date)->endOfMonth()->addDays(10);
        if ($now->greaterThan($limitDate)) {
            throw new \Exception("Prazo de homologação expirado. Registros só podem ser homologados até o dia 10 do mês seguinte.");
        }

        if ($attendanceRecord->status !== 'submitted') {
            throw new \Exception("Somente registros enviados podem ser aprovados.");
        }

        $attendanceRecord->update([
            'status' => 'approved',
            'approved' => true,
            'approved_by_user_id' => Auth::id(),
        ]);

        return $attendanceRecord;
    }

    /**
     * Coordenador recusa registro
     */
    public function rejectRecord(AttendanceRecord $attendanceRecord, string $reason): AttendanceRecord
    {
        if ($attendanceRecord->status !== 'submitted') {
            throw new \Exception("Somente registros enviados podem ser recusados.");
        }

        $attendanceRecord->update([
            'status' => 'rejected',
            'approved' => false,
            'approved_by_user_id' => Auth::id(),
            'rejection_reason' => $reason,
        ]);

        return $attendanceRecord;
    }

    /**
     * Relatório consolidado da unidade
     */
    public function generateReport(?int $unitId, int $month, int $year)
    {
        $query = AttendanceRecord::query()
            ->where('status', 'approved')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        // Se unidade for informada, filtra
        if ($unitId) {
            $query->whereHas('scholarshipHolder', fn($q) => $q->where('unit_id', $unitId));
        }

        return $query
            ->selectRaw('scholarship_holder_id, SUM(hours) as total_hours, SUM(calculated_value) as total_value')
            ->groupBy('scholarship_holder_id')
            ->with(['scholarshipHolder' => function($q) {
                $q->select('id','name','cpf','phone','bank','agency','account', 'user_id','unit_id')
                ->with(['unit:id,name']);
            }])
            ->get();
    }

     public function listForUser($userId)
    {
        return AttendanceRecord::where('user_id', $userId)->latest()->get();
    }

    public function isEditable(AttendanceRecord $attendanceRecord): bool
    {
        // Só pode editar se estiver em rascunho ou rejeitado
        return in_array($attendanceRecord->status, ['draft', 'rejected']);
    }

    public function create(array $data): AttendanceRecord
    {
        $data['scholarship_holder_id'] = Auth::user()->scholarshipHolder->id ?? null;
        return AttendanceRecord::create($data);
    }

    public function update(AttendanceRecord $attendanceRecord, array $data): AttendanceRecord
    {
        $attendanceRecord->update($data);
        return $attendanceRecord;
    }

    public function submit(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $attendanceRecord->update(['status' => AttendanceRecord::STATUS_SUBMITTED]);
        return $attendanceRecord;
    }

    public function delete(AttendanceRecord $attendanceRecord)
    {
        return $attendanceRecord->delete();
    }

    public function approve(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $attendanceRecord->update(['status' => AttendanceRecord::STATUS_APPROVED]);
        return $attendanceRecord;
    }

    public function reject(AttendanceRecord $attendanceRecord): AttendanceRecord
    {
        $attendanceRecord->update(['status' => AttendanceRecord::STATUS_REJECTED]);
        return $attendanceRecord;
    }
}
