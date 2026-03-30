<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Unit;

class PaymentService
{
    public function monthly(Unit $unit, int $month, int $year)
    {

        $records = AttendanceRecord::with([
            'scholarshipHolder.user',
            'scholarshipHolder.projects.positions',
            'scholarshipHolder.unit'
        ])
        ->whereYear('date',$year)
        ->whereMonth('date',$month)
        ->whereHas('scholarshipHolder', fn($q)=>
            $q->where('unit_id',$unit->id)
        )
        ->get();

        return $records->groupBy('scholarship_holder_id')
            ->map(function($records){

                $holder = $records->first()->scholarshipHolder;
                $project = $holder->projects->first();

                $positionId = $project?->pivot->position_id;

                $rate = $project?->positions
                    ->firstWhere('id',$positionId)
                    ?->pivot->hourly_rate ?? 0;

                $hours = $records->sum('hours');

                return [
                    'holder' => $holder->user->name,
                    'cpf' => $holder->cpf,
                    'bank' => $holder->bank,
                    'agency' => $holder->agency,
                    'account' => $holder->account,
                    'hours' => $hours,
                    'rate' => $rate,
                    'total' => $hours * $rate,
                ];
            });
    }
}