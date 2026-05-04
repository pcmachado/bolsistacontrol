<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Payment;
use App\Models\Unit;
use App\Models\User;
use App\Support\Traits\PaymentFilters;
use Illuminate\Http\Request;

class PaymentService
{
    use PaymentFilters;

    public function monthly(Unit $unit, int $month, int $year)
    {

        $records = AttendanceRecord::with([
            'scholarshipHolder.user',
            'scholarshipHolder.projects.positions',
            'scholarshipHolder.unit',
        ])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereHas('scholarshipHolder', fn ($q) => $q->where('unit_id', $unit->id)
            )
            ->get();

        return $records->groupBy('scholarship_holder_id')
            ->map(function ($records) {

                $holder = $records->first()->scholarshipHolder;
                $project = $holder->projects->first();

                $positionId = $project?->pivot->position_id;

                $rate = $project?->positions
                    ->firstWhere('id', $positionId)
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

    public function summary(User $user, Request $request): array
    {
        $query = Payment::query()
            ->with(['unit', 'project', 'scholarshipHolder.projects']);

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

        $query = $this->applyPaymentFilters($query, $request);

        $payments = $query->get();

        return [
            'totalGeral' => $payments->sum('amount'),

            'grouped' => $payments->groupBy('unit_id')
                ->map(function ($items) {
                    return [
                        'unit' => $items->first()->unit?->name,
                        'count' => $items->count(),
                        'total' => $items->sum('amount'),
                    ];
                }),
        ];
    }
}
