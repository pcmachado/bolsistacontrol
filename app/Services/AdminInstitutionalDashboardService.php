<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminInstitutionalDashboardService
{
    /* =========================================================
     |  DASHBOARD OPERACIONAL (FREQUÊNCIA)
     ========================================================= */
    public function getDashboardData(array $filters, ?int $projectId = null): array
    {
        $user = Auth::user();

        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);
        $currentMonth = now()->format('Y-m');

        $scope = $this->resolveScope($user);

        $unitName = $user->unit ? $user->unit->name : null;

        $projects = $this->resolveUserProjects($user);

        $activeProjectId = $projectId
            ?? request('project_id')
            ?? $projects->first()?->id;

        $activeProject = $projects->firstWhere('id', $activeProjectId);

        /* -------------------------------
         | Acadêmico (somente visão ampla)
         ------------------------------- */
        $academic = [];
        if (in_array($scope, ['all', 'institution'])) {
            $academic = [
                'projects_active' => Project::query()
                    ->where('status', Project::STATUS_ACTIVE)->count(),
                'projects_draft' => Project::query()
                    ->where('status', Project::STATUS_DRAFT)->count(),
                'courses_total' => Course::count('id'),
                'disciplines_total' => Discipline::count('id'),
                'class_offerings_active' => ClassOffering::count('id'),
            ];
        }

        $query = AttendanceSubmission::query()
            ->with('scholarshipHolder.user');

        // aplica o mesmo escopo do dashboard
        $query = app(VisibilityService::class)
            ->apply($query, $user, 'self');

        if ($activeProjectId) {
            $query->whereHas('scholarshipHolder.projects', function (Builder $q) use ($activeProjectId) {
                $q->where('projects.id', $activeProjectId);
            });
        }

        $lastSubmissions = (clone $query)
            ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $lastApprovals = (clone $query)
            ->where('status', AttendanceSubmission::STATUS_APPROVED)
            ->latest('approved_at')
            ->take(5)
            ->get();

        /* -------------------------------
         | Alertas
         ------------------------------- */
        $alerts = [];

        if (in_array($scope, ['all', 'institution'])) {

            $attendance = app(AttendanceDashboardService::class)
                ->submissionCounts($user);

            $alerts = [
                'attendance_submitted' => $attendance['submitted'],
                'attendance_rejected' => $attendance['rejected'],
                'payments_pending_execution' => Payment::query()
                    ->where('status', Payment::STATUS_SENT)->count(),
                'payments_waiting_confirmation' => Payment::query()
                    ->where('status', Payment::STATUS_PAID)->count(),
            ];
        }

        /* -------------------------------
         | Contadores globais
         ------------------------------- */
        if ($month) {
            $query->where('year', $year)->where('month', $month);
        } else {
            $query->whereBetween(
                DB::raw("DATE(CONCAT(year,'-',LPAD(month,2,'0'),'-01'))"),
                [$startDate->startOfMonth(), $endDate->endOfMonth()]
            );
        }

        $total = $query->count();

        $counts = [
            'approved' => (clone $query)->where('status', AttendanceSubmission::STATUS_APPROVED)->count(),
            'submitted' => (clone $query)->where('status', AttendanceSubmission::STATUS_SUBMITTED)->count(),
            'rejected' => (clone $query)->where('status', AttendanceSubmission::STATUS_REJECTED)->count(),
        ];

        [$usersCount, $scholarshipHoldersCount, $totalUnidades, $unidades] =
            $this->resolveBaseCounters($user, $scope);

        $percentages = collect($counts)->map(
            fn ($c) => $total ? round($c / $total * 100, 1) : 0
        );

        return compact(
            'counts',
            'percentages',
            'academic',
            'alerts',
            'lastSubmissions',
            'lastApprovals',
            'usersCount',
            'scholarshipHoldersCount',
            'totalUnidades',
            'unidades',
            'unitName',
            'currentMonth',
            'month',
            'year',
            'projects',
            'activeProject',
            'activeProjectId'
        );
    }

    public function getAdminDashboardData(array $filters, $user, $request): array
    {
        $projects = $this->resolveProjects($user);

        $activeProject = $this->resolveActiveProject($projects, $request);

        $data = $this->getDashboardData($filters, $activeProject?->id);
        $financial = $this->getFinancialData($filters, $activeProject?->id);

        return [
            ...$data,
            'financialOverview' => $financial,
            'projects' => $projects,
            'activeProject' => $activeProject,
            'activeProjectId' => $activeProject?->id,
        ];
    }

    /* =========================================================
     |  DASHBOARD FINANCEIRO
     ========================================================= */
    public function getFinancialData(array $filters, ?int $projectId = null): array
    {
        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);

        $user = Auth::user();
        $scope = $this->resolveScope($user);

        $projects = $this->resolveUserProjects($user);

        $activeProjectId = $projectId
            ?? request('project_id')
            ?? $projects->first()?->id;

        $activeProject = $projects->firstWhere('id', $activeProjectId);

        $query = Payment::query();

        if ($activeProjectId) {
            $query->whereHas('scholarshipHolder.projects', function (Builder $q) use ($activeProjectId) {
                $q->where('projects.id', $activeProjectId);
            });
        }

        if ($month) {
            $query->where('year', $year)->where('month', $month);
        } else {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($q2) use ($startDate) {
                    $q2->where('year', '>', $startDate->year)
                        ->orWhere(function ($q3) use ($startDate) {
                            $q3->where('year', $startDate->year)
                                ->where('month', '>=', $startDate->month);
                        });
                })
                    ->where(function ($q2) use ($endDate) {
                        $q2->where('year', '<', $endDate->year)
                            ->orWhere(function ($q3) use ($endDate) {
                                $q3->where('year', $endDate->year)
                                    ->where('month', '<=', $endDate->month);
                            });
                    });
            });
        }

        $this->applyPaymentScope($query, $user, $scope);

        $counts = [
            'generated' => (clone $query)->count('id'),
            'sent' => (clone $query)->where('status', Payment::STATUS_SENT)->count(),
            'paid' => (clone $query)->where('status', Payment::STATUS_PAID)->count(),
            'confirmed' => (clone $query)->where('status', Payment::STATUS_CONFIRMED)->count(),
        ];

        $totals = [
            'sent' => (clone $query)->where('status', Payment::STATUS_SENT)->sum('amount'),
            'paid' => (clone $query)->where('status', Payment::STATUS_PAID)->sum('amount'),
            'confirmed' => (clone $query)->where('status', Payment::STATUS_CONFIRMED)->sum('amount'),
        ];

        return compact('counts', 'totals');
    }

    /* =========================================================
     |  HELPERS
     ========================================================= */
    private function resolveScope(User $user): string
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return 'all';
        }

        if ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            return 'institution';
        }

        if ($user->hasRole(['coordenador_adjunto', 'supervisor', 'apoio_administrativo', 'orientador'])) {
            return 'unit';
        }

        return $user->scholarshipHolder ? 'self' : 'none';
    }

    private function applyPaymentScope($query, User $user, string $scope): void
    {
        match ($scope) {
            'institution' => $query->whereHas('unit', fn ($q) => $q->where('institution_id', $user->institution_id)
            ),

            'unit' => $query->where('unit_id', $user->unit_id),

            'self' => $query->where('scholarship_holder_id', $user->scholarshipHolder->id),

            'none' => $query->whereRaw('1 = 0'),

            default => null,
        };
    }

    private function resolveBaseCounters(User $user, string $scope): array
    {
        $users = User::query();
        $holders = ScholarshipHolder::query();
        $units = Unit::query();

        if ($scope === 'unit') {
            $users->where('unit_id', $user->unit_id);
            $holders->where('unit_id', $user->unit_id);
            $units->where('id', $user->unit_id);
        }

        if ($scope === 'self') {
            $users->where('id', $user->id);
            $holders->where('id', $user->scholarshipHolder->id);
            $units->where('id', $user->scholarshipHolder->unit_id);
        }

        return [
            $users->count('id'),
            $holders->count('id'),
            $units->count(),
            $units->get(),
        ];
    }

    private function resolvePeriod(array $filters): array
    {
        if (! empty($filters['month']) && preg_match('/^\d{4}-\d{2}$/', $filters['month'])) {
            [$year, $month] = explode('-', $filters['month']);

            return [
                (int) $year,
                (int) $month,
                Carbon::create($year, $month, 1)->startOfMonth(),
                Carbon::create($year, $month, 1)->endOfMonth(),
            ];
        }

        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $start = Carbon::parse($filters['start_date'])->startOfDay();
            $end = Carbon::parse($filters['end_date'])->endOfDay();

            return [(int) $start->year, null, $start, $end];
        }

        $now = now();

        return [(int) $now->year, (int) $now->month, $now->startOfMonth(), $now->endOfMonth()];
    }

    public function resolveProjects($user)
    {
        return $user->assignments()
            ->whereNotNull('project_id')
            ->where('active', true)
            ->pluck('project_id')
            ->unique()
            ->map(fn ($id) => \App\Models\Project::find($id))
            ->filter();
    }

    public function resolveActiveProject($projects, $request)
    {
        $projectId = $request->input('project_id');

        return $projects->firstWhere('id', $projectId)
            ?? $projects->first();
    }
}
