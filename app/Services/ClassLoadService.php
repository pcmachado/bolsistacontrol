<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
use App\Models\ClassSession;
use Carbon\Carbon;

class ClassLoadService
{
    public function getMonthlyLoad(
        int $classOfferingId,
        int $disciplineId,
        int $month,
        int $year
    ): float {
        return (float) ClassSession::query()
            ->where('class_offering_id', $classOfferingId)
            ->where('discipline_id', $disciplineId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('duration_hours');
    }

    public function getPlannedTotalHours(ClassOfferingDiscipline $discipline): float
    {
        return (float) (
            $discipline->planned_total_hours
            ?? $discipline->workload
            ?? $discipline->discipline?->workload
            ?? 0
        );
    }

    public function getHoursPerDay(ClassOfferingDiscipline $discipline): float
    {
        return (float) (
            $discipline->hours_per_day
            ?? $discipline->classOffering?->hours_per_day
            ?? 0
        );
    }

    public function getPlannedClassDays(ClassOfferingDiscipline $discipline): int
    {
        $hoursPerDay = $this->getHoursPerDay($discipline);

        if ($hoursPerDay <= 0) {
            return 0;
        }

        return (int) ceil($this->getPlannedTotalHours($discipline) / $hoursPerDay);
    }

    public function getMonthlyPlanning(
        ClassOffering $offering,
        ClassOfferingDiscipline $discipline,
        int $month,
        int $year
    ): array {
        $hoursPerDay = $this->getHoursPerDay($discipline);
        $plannedTotalHours = $this->getPlannedTotalHours($discipline);
        $plannedClassDays = $this->getPlannedClassDays($discipline);

        $sessionHours = $this->getMonthlyLoad(
            $offering->id,
            $discipline->discipline_id,
            $month,
            $year
        );

        $monthlyClassDays = $hoursPerDay > 0 && $sessionHours > 0
            ? (int) ceil($sessionHours / $hoursPerDay)
            : $this->distributeClassDaysAcrossMonths(
                $offering,
                $plannedClassDays,
                $month,
                $year
            );

        $monthlyPlannedHours = $sessionHours > 0
            ? $sessionHours
            : min($plannedTotalHours, $monthlyClassDays * $hoursPerDay);

        return [
            'hours_per_day' => $hoursPerDay,
            'planned_total_hours' => $plannedTotalHours,
            'planned_class_days' => $plannedClassDays,
            'monthly_planned_hours' => $monthlyPlannedHours,
            'monthly_class_days' => $monthlyClassDays,
        ];
    }

    private function distributeClassDaysAcrossMonths(
        ClassOffering $offering,
        int $plannedClassDays,
        int $month,
        int $year
    ): int {
        if ($plannedClassDays <= 0 || ! $offering->start_date) {
            return 0;
        }

        $start = Carbon::parse($offering->start_date)->startOfMonth();
        $end = $offering->end_date
            ? Carbon::parse($offering->end_date)->startOfMonth()
            : $start->copy();
        $target = Carbon::create($year, $month, 1)->startOfMonth();

        if ($target->lt($start) || $target->gt($end)) {
            return 0;
        }

        $monthCount = (int) $start->diffInMonths($end) + 1;
        $offset = (int) $start->diffInMonths($target);
        $base = intdiv($plannedClassDays, $monthCount);
        $remainder = $plannedClassDays % $monthCount;

        return $base + ($offset < $remainder ? 1 : 0);
    }
}
