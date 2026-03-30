<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Unit;

class ReportService
{
    public function unitAttendance(Unit $unit, int $month, int $year)
    {
        $attendances = AttendanceRecord::with([
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
            'scholarshipHolder.projects.positions'
        ])
        ->whereYear('date',$year)
        ->whereMonth('date',$month)
        ->whereHas('scholarshipHolder', fn($q)=>
            $q->where('unit_id',$unit->id)
        )
        ->get();

        return $attendances->groupBy('scholarship_holder_id')
            ->map(function ($records) {

                $holder = $records->first()->scholarshipHolder;
                $project = $holder->projects->first();

                $positionId = $project?->pivot->position_id;

                $hourlyRate = $project?->positions
                    ->firstWhere('id',$positionId)
                    ?->pivot->hourly_rate ?? 0;

                $totalHours = $records->sum('hours');

                return [
                    'unit' => $holder->unit->name ?? null,
                    'name' => $holder->user->name,
                    'hours' => $totalHours,
                    'rate' => $hourlyRate,
                    'total' => $totalHours * $hourlyRate,
                ];
            });
    }
}