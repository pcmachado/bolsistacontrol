<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\FinancialClosure;
use App\Models\Project;
use App\Models\Unit;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;

class PaymentDashboardService
{
    public function data($user, $filters)
    {
        $month     = $filters['month'];
        $year      = $filters['year'];
        $projectId = $filters['project_id'] ?? null;
        $unitId    = $filters['unit_id'] ?? null;

        $baseQuery = Payment::query($user)
            ->where('year', $year)
            ->where('month', $month)
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn($q) => $q->where('unit_id', $unitId));

        $baseQuery = app(VisibilityService::class)
            ->apply($baseQuery, $user, 'self');

        // =====================
        // Totais principais
        // =====================

        $totalPaid = (clone $baseQuery)
            ->where('status', Payment::STATUS_PAID)
            ->sum('amount');

        $totalConfirmed = (clone $baseQuery)
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('amount');

        $totalPending = (clone $baseQuery)
            ->where('status', Payment::STATUS_SENT)
            ->sum('amount');

        $countPending = (clone $baseQuery)
            ->where('status', Payment::STATUS_SENT)
            ->count();

        // =====================
        // Período anterior
        // =====================

        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear  = $month == 1 ? $year - 1 : $year;

        $previousTotal = Payment::query($user)
            ->where('year', $prevYear)
            ->where('month', $prevMonth)
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
            ->whereIn('status', [
                Payment::STATUS_PAID,
                Payment::STATUS_CONFIRMED
            ])
            ->sum('amount');

        $currentTotal = $totalPaid + $totalConfirmed;

        $variation = $previousTotal > 0
            ? (($currentTotal - $previousTotal) / $previousTotal) * 100
            : null;

        // =====================
        // Gráficos
        // =====================

        $chartByProject = (clone $baseQuery)
            ->selectRaw('project_id, SUM(amount) as total')
            ->groupBy('project_id')
            ->with('project')
            ->get();

        $chartByUnit = (clone $baseQuery)
            ->selectRaw('unit_id, SUM(amount) as total')
            ->groupBy('unit_id')
            ->with('unit')
            ->get();

        // =====================
        // Evolução anual
        // =====================

        $yearly = Payment::query($user)
            ->where('year', $year)
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
            ->selectRaw('month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total','month');

        $monthlyTotals = collect(range(1,12))->map(fn($m) => [
            'month' => $m,
            'total' => $yearly[$m] ?? 0
        ]);

        return [
            'month' => $month,
            'year'  => $year,
            'projects' => Project::orderBy('name')->get(),
            'units'    => Unit::orderBy('name')->get(),

            'totalPaid' => $totalPaid,
            'totalConfirmed' => $totalConfirmed,
            'totalPending' => $totalPending,
            'countPending' => $countPending,

            'previousTotal' => $previousTotal,
            'currentTotal'  => $currentTotal,
            'variation'     => $variation,
            'prevMonth'     => $prevMonth,
            'prevYear'      => $prevYear,

            'chartByProject' => $chartByProject,
            'chartByUnit'    => $chartByUnit,
            'monthlyTotals'  => $monthlyTotals,

            'isClosed' => FinancialClosure::isClosed($unitId, $month, $year),
        ];
    }
}
