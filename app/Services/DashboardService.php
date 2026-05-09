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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData(array $filters, ?int $projectId = null): array
    {
        $user = Auth::user();
        $visibility = app(VisibilityService::class);

        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);

        $currentMonth = now()->format('Y-m');
        $scope = $this->resolveScope($user);
        $unitName = $user->unit?->name;

        $projects = $this->resolveUserProjects($user);
        $activeProjectId = $projectId;
        $activeProject = $projects->firstWhere('id', $activeProjectId);

        $query = $visibility->apply(
            AttendanceSubmission::query()->with('scholarshipHolder.user'),
            $user,
            'admin'
        );

        if ($activeProjectId) {
            $query->where('project_id', $activeProjectId);
        }

        $alerts = [];

        if (in_array($scope, ['all', 'institution'], true)) {
            $attendance = app(AttendanceDashboardService::class)->submissionCounts($user);

            $alerts = [
                'attendance_submitted' => $attendance['submitted'],
                'attendance_rejected' => $attendance['rejected'],
                'payments_pending_execution' => $visibility
                    ->apply(Payment::query(), $user, 'admin')
                    ->where('status', Payment::STATUS_SENT)
                    ->count(),
                'payments_waiting_confirmation' => $visibility
                    ->apply(Payment::query(), $user, 'admin')
                    ->where('status', Payment::STATUS_PAID)
                    ->count(),
            ];
        }

        if ($month) {
            $query->where('year', $year)->where('month', $month);
        } else {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();

        $counts = [
            'approved' => (clone $query)->where('status', AttendanceSubmission::STATUS_APPROVED)->count(),
            'submitted' => (clone $query)->where('status', AttendanceSubmission::STATUS_SUBMITTED)->count(),
            'rejected' => (clone $query)->where('status', AttendanceSubmission::STATUS_REJECTED)->count(),
        ];

        [$usersCount, $scholarshipHoldersCount, $totalUnidades, $unidades] = $this->resolveBaseCounters($user, $scope);

        $percentages = collect($counts)->map(
            fn ($count) => $total ? round($count / $total * 100, 1) : 0
        );

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

        return [
            'counts' => $counts,
            'percentages' => $percentages,
            'projects' => $projects,
            'activeProject' => $activeProject,
            'activeProjectId' => $activeProjectId,
            'academic' => [
                'projects_active' => $visibility
                    ->apply(Project::query(), $user, 'admin')
                    ->when($activeProjectId, fn ($q) => $q->where('id', $activeProjectId))
                    ->where('status', Project::STATUS_ACTIVE)
                    ->count(),
                'projects_draft' => $visibility
                    ->apply(Project::query(), $user, 'admin')
                    ->when($activeProjectId, fn ($q) => $q->where('id', $activeProjectId))
                    ->where('status', Project::STATUS_DRAFT)
                    ->count(),
                'courses_total' => $visibility
                    ->apply(Course::query(), $user, 'admin')
                    ->when($activeProjectId, fn ($q) => $q->where('id', $activeProjectId))
                    ->count(),
                'disciplines_total' => $visibility
                    ->apply(Discipline::query(), $user, 'admin')
                    ->when($activeProjectId, fn ($q) => $q->where('id', $activeProjectId))
                    ->count('id'),
                'class_offerings_active' => $visibility
                    ->apply(ClassOffering::query(), $user, 'admin')
                    ->when($activeProjectId, fn ($q) => $q->where('id', $activeProjectId))
                    ->where('active', true)
                    ->count(),
            ],
            'lastSubmissions' => $lastSubmissions,
            'lastApprovals' => $lastApprovals,
            'alerts' => $alerts,
            'unitName' => $unitName,
            'usersCount' => $usersCount,
            'scholarshipHoldersCount' => $scholarshipHoldersCount,
            'totalUnidades' => $totalUnidades,
            'unidades' => $unidades,
            'currentMonth' => $currentMonth,
            'month' => $month,
            'year' => $year,
        ];
    }

    public function getFinancialData(array $filters, ?int $projectId = null): array
    {
        [$year, $month] = $this->resolvePeriod($filters);

        $query = app(VisibilityService::class)
            ->apply(Payment::query(), Auth::user(), 'admin');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($month) {
            $query->where('year', $year)->where('month', $month);
        }

        return [
            'counts' => [
                'generated' => $query->count(),
            ],
            'totals' => [
                'paid' => $query->sum('amount'),
            ],
        ];
    }

    private function resolveUserProjects(User $user): Collection
    {
        $projectIds = $user->visibleProjectIds();

        if ($projectIds->isEmpty()) {
            return collect();
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $projectIds)
            ->orderBy('name')
            ->get();
    }

    private function resolveScope(User $user): string
    {
        if ($user->isInstitutionScoped()) {
            return 'all';
        }

        if ($user->isUnitScoped()) {
            return 'unit';
        }

        return $user->scholarshipHolder ? 'self' : 'none';
    }

    private function resolveBaseCounters(User $user, string $scope): array
    {
        $users = User::query()->with('unit');
        $holders = ScholarshipHolder::query();
        $units = Unit::query()->withoutGlobalScopes();

        $institutionIds = $user->activeInstitutionIds();
        $unitIds = $user->visibleUnitIds();

        if ($scope === 'all' && $institutionIds->isNotEmpty()) {
            $users->where(function ($query) use ($institutionIds) {
                $query->whereIn('institution_id', $institutionIds)
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->whereIn('institution_id', $institutionIds));
            });
            $holders->whereHas('unit', fn ($query) => $query->whereIn('institution_id', $institutionIds));
            $units->whereIn('institution_id', $institutionIds);
        }

        if ($scope === 'unit' && $unitIds->isNotEmpty()) {
            $users->whereIn('unit_id', $unitIds);
            $holders->whereIn('unit_id', $unitIds);
            $units->whereIn('id', $unitIds);
        }

        if ($scope === 'self' && $user->scholarshipHolder) {
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
        if (! empty($filters['month'])) {
            [$year, $month] = explode('-', $filters['month']);

            return [(int) $year, (int) $month, null, null];
        }

        $start = Carbon::parse($filters['start_date'] ?? now()->startOfMonth());
        $end = Carbon::parse($filters['end_date'] ?? now()->endOfMonth());

        return [(int) $start->year, null, $start, $end];
    }

    public function resolveProjects($user)
    {
        return $this->resolveUserProjects($user);
    }

    public function resolveActiveProject($projects, $request)
    {
        $projectId = $request->input('project_id');

        return $projects->firstWhere('id', $projectId) ?? $projects->first();
    }
}
