<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use DomainException;

class AttendanceService
{
    public function getMonthlyTotal(ScholarshipHolder $holder, int $year, int $month): float
    {
        return AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('hours');
    }

    public function getMonthlyLimit(ScholarshipHolder $holder): float
    {
        $project = $holder->projects->first();

        return ($project->pivot->weekly_workload ?? 0) * 4;
    }

    public function validateMonthlyLimit(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        float $newHours,
        ?int $ignoreRecordId = null
    ): void {

        $query = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($ignoreRecordId) {
            $query->where('id', '!=', $ignoreRecordId);
        }

        $total = $query->sum('hours');

        $limit = $this->getMonthlyLimit($holder);

        if (($total + $newHours) > $limit) {
            throw new DomainException(
                "Limite mensal excedido ({$limit}h)."
            );
        }
    }
}