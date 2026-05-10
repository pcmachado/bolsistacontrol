<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use DomainException;

class AttendanceService
{
    public function getMonthlyTotal(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        ?int $projectId = null
    ): float {
        return AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('hours');
    }

    public function getMonthlyLimit(
        ScholarshipHolder $holder,
        ?int $projectId = null
    ): float {
        $project = $projectId
            ? $holder->projects()->where('projects.id', $projectId)->first()
            : $holder->projects()->first();

        if (! $project) {
            return 0;
        }

        return $project->weeklyWorkloadForScholarshipHolder($holder) * 4;
    }

    public function validateMonthlyLimit(
        ScholarshipHolder $holder,
        int $year,
        int $month,
        float $newHours,
        ?int $ignoreRecordId = null,
        ?int $projectId = null
    ): void {
        $query = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($projectId, fn ($builder) => $builder->where('project_id', $projectId))
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($ignoreRecordId) {
            $query->where('id', '!=', $ignoreRecordId);
        }

        $total = $query->sum('hours');
        $limit = $this->getMonthlyLimit($holder, $projectId);

        if (($total + $newHours) > $limit) {
            throw new DomainException(
                "Limite mensal excedido ({$limit}h)."
            );
        }
    }
}
