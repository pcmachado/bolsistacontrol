<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\FinancialClosure;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectFundingSource;
use App\Models\Unit;
use App\Models\User;

class PaymentDashboardService
{
    public function data(User $user, array $filters)
    {
        $month = $filters['month'];
        $year = $filters['year'];
        $projectId = $filters['project_id'] ?? null;
        $unitId = $filters['unit_id'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY (PADRÃO ÚNICO DO SISTEMA)
        |--------------------------------------------------------------------------
        */
        $baseQuery = Payment::query()
            ->where('year', $year)
            ->where('month', $month)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn ($q) => $q->where('unit_id', $unitId));

        $baseQuery = app(VisibilityService::class)
            ->apply($baseQuery, $user, 'admin');

        /*
        |--------------------------------------------------------------------------
        | TOTAIS (CARDS)
        |--------------------------------------------------------------------------
        */
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

        $countPaid = (clone $baseQuery)
            ->where('status', Payment::STATUS_PAID)
            ->count();

        $countConfirmed = (clone $baseQuery)
            ->where('status', Payment::STATUS_CONFIRMED)
            ->count();

        $totalCount = (clone $baseQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | COMPARAÇÃO MENSAL
        |--------------------------------------------------------------------------
        */
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear = $month == 1 ? $year - 1 : $year;

        $previousQuery = Payment::query()
            ->where('year', $prevYear)
            ->where('month', $prevMonth)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn ($q) => $q->where('unit_id', $unitId));

        $previousQuery = app(VisibilityService::class)
            ->apply($previousQuery, $user, 'admin');

        $previousTotal = $previousQuery
            ->whereIn('status', [
                Payment::STATUS_PAID,
                Payment::STATUS_CONFIRMED,
            ])
            ->sum('amount');

        $currentTotal = $totalPaid + $totalConfirmed;
        $currentTotalCount = $countPaid + $countConfirmed;

        $variation = $previousTotal > 0
            ? (($currentTotal - $previousTotal) / $previousTotal) * 100
            : null;

        $forecastQuery = AttendanceSubmission::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', AttendanceSubmission::STATUS_APPROVED)
            ->when($unitId, fn ($q) => $q->whereHas('scholarshipHolder', fn ($h) => $h->where('unit_id', $unitId)
            )
            );

        $forecastTotal = (clone $forecastQuery)->sum('calculated_value');
        $forecastCount = (clone $forecastQuery)->count();

        $realTotal = $totalPaid + $totalConfirmed;

        $gap = $forecastTotal - $realTotal;

        /*
                |--------------------------------------------------------------------------
                | ORÇAMENTO DOS PROJETOS (PROJECT_FUNDING_SOURCE)
                |--------------------------------------------------------------------------
                */
        $budgetQuery = ProjectFundingSource::query()
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn ($q) => $q->whereHas('project.classOfferings', fn ($sub) => $sub->where('unit_id', $unitId)
            ));

        if ($user->isInstitutionScoped()) {
            $budgetQuery->whereHas('project.classOfferings.unit', fn ($q) => $q->whereIn('institution_id', $user->activeInstitutionIds())
            );
        } elseif ($user->isUnitScoped()) {
            $budgetQuery->whereHas('project.classOfferings', fn ($q) => $q->whereIn('unit_id', $user->visibleUnitIds())
            );
        }

        $budgetAllocated = (float) (clone $budgetQuery)->sum('allocated_amount');
        $budgetUsed = (float) (clone $budgetQuery)->sum('used_amount');
        $budgetAvailable = max($budgetAllocated - $budgetUsed, 0);
        $budgetCommitmentPercent = $budgetAllocated > 0
            ? min(100, round(($budgetUsed / $budgetAllocated) * 100, 1))
            : 0;

        $projectBudgetSummaries = (clone $budgetQuery)
            ->selectRaw('project_id, SUM(COALESCE(allocated_amount, 0)) as allocated_total, SUM(COALESCE(used_amount, 0)) as used_total')
            ->groupBy('project_id')
            ->with('project')
            ->get()
            ->map(function ($row) {
                $allocated = (float) ($row->allocated_total ?? 0);
                $used = (float) ($row->used_total ?? 0);
                $available = max($allocated - $used, 0);

                return [
                    'project_id' => $row->project_id,
                    'project_name' => $row->project?->name ?? 'Projeto',
                    'allocated_total' => $allocated,
                    'used_total' => $used,
                    'available_total' => $available,
                    'commitment_percent' => $allocated > 0
                        ? min(100, round(($used / $allocated) * 100, 1))
                        : 0,
                ];
            })
            ->sortBy('project_name')
            ->values();

        $activeProjectBudgetId = $projectId ?: $projectBudgetSummaries->first()['project_id'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | GRÁFICOS (PADRÃO BASE QUERY)
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | STACKED (EVOLUÇÃO POR STATUS)
        |--------------------------------------------------------------------------
        */
        $yearQuery = Payment::query()
            ->where('year', $year)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($unitId, fn ($q) => $q->where('unit_id', $unitId));

        $yearQuery = app(VisibilityService::class)
            ->apply($yearQuery, $user, 'admin');

        $yearlyRaw = $yearQuery
            ->selectRaw('month, status, SUM(amount) as total')
            ->groupBy('month', 'status')
            ->get();

        $months = collect(range(1, 12));

        $monthlyPaid = [];
        $monthlyConfirmed = [];
        $monthlyPending = [];

        foreach ($months as $m) {
            $monthlyPaid[] = $yearlyRaw->where('month', $m)
                ->where('status', Payment::STATUS_PAID)->sum('total');

            $monthlyConfirmed[] = $yearlyRaw->where('month', $m)
                ->where('status', Payment::STATUS_CONFIRMED)->sum('total');

            $monthlyPending[] = $yearlyRaw->where('month', $m)
                ->where('status', Payment::STATUS_SENT)->sum('total');
        }

        return [
            'month' => $month,
            'year' => $year,

            'projects' => Project::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $user->visibleProjectIds())
                ->orderBy('name', 'asc')
                ->get(),
            'units' => Unit::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $user->visibleUnitIds())
                ->orderBy('name', 'asc')
                ->get(),

            'totalPaid' => $totalPaid,
            'totalConfirmed' => $totalConfirmed,
            'totalPending' => $totalPending,
            'countPending' => $countPending,
            'countPaid' => $countPaid,
            'countConfirmed' => $countConfirmed,
            'totalCount' => $totalCount,

            'previousTotal' => $previousTotal,
            'currentTotal' => $currentTotal,
            'currentTotalCount' => $currentTotalCount,
            'variation' => $variation,
            'prevMonth' => $prevMonth,
            'prevYear' => $prevYear,

            'chartByProject' => $chartByProject,
            'chartByUnit' => $chartByUnit,

            'chartStacked' => [
                'months' => $months->map(fn ($m) => str_pad($m, 2, '0', STR_PAD_LEFT)),
                'paid' => $monthlyPaid,
                'confirmed' => $monthlyConfirmed,
                'pending' => $monthlyPending,
            ],

            'isClosed' => FinancialClosure::isClosed($unitId, $month, $year),

            'forecastTotal' => $forecastTotal,
            'forecastCount' => $forecastCount,
            'realTotal' => $realTotal,
            'gap' => $gap,

            'budgetAllocated' => $budgetAllocated,
            'budgetUsed' => $budgetUsed,
            'budgetAvailable' => $budgetAvailable,
            'budgetCommitmentPercent' => $budgetCommitmentPercent,
            'projectBudgetSummaries' => $projectBudgetSummaries,
            'activeProjectBudgetId' => $activeProjectBudgetId,
        ];
    }
}
