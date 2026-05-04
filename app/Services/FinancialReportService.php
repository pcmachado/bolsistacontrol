<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FinancialReportService
{
    public function query(array $filters): Builder
    {
        [$year, $month] = $this->parseMonth($filters['month'] ?? null);

        $user = Auth::user();

        $query = app(VisibilityService::class)->apply(
            Payment::with(['unit', 'project', 'scholarshipHolder.user']),
            $user,
            'admin'
        );

        if ($year) {
            $query->where('year', $year);
        }

        if ($month) {
            $query->where('month', $month);
        }

        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (! empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->orderBy('year')->orderBy('month');
    }

    public function get(array $filters): Collection
    {
        return $this->query($filters)->get();
    }

    public function summary(Collection $payments): array
    {
        return [
            'total' => $payments->sum('amount'),
            'byUnit' => $payments
                ->groupBy(fn ($payment) => $payment->unit->name ?? 'Sem unidade')
                ->map(fn ($group) => $group->sum('amount')),
            'byProject' => $payments
                ->groupBy(fn ($payment) => $payment->project->name ?? 'Sem projeto')
                ->map(fn ($group) => $group->sum('amount')),
            'byMonth' => $payments
                ->groupBy('month')
                ->map(fn ($group) => $group->sum('amount')),
            'byStatus' => $payments
                ->groupBy('status')
                ->map(fn ($group) => $group->sum('amount')),
        ];
    }

    public function institutionalSummary(Collection $payments): array
    {
        return [
            'total_paid' => $payments->sum('amount'),
            'avg_monthly' => $payments->groupBy('month')->map->sum('amount')->avg(),
            'active_units' => $payments->groupBy('unit_id')->count(),
            'active_projects' => $payments->groupBy('project_id')->count(),
            'active_bolsistas' => $payments->groupBy('scholarship_holder_id')->count(),
        ];
    }

    protected function parseMonth(?string $month): array
    {
        if (! $month) {
            return [null, null];
        }

        if (str_contains($month, '-')) {
            [$year, $resolvedMonth] = explode('-', $month);

            return [(int) $year, (int) $resolvedMonth];
        }

        return [null, null];
    }
}
