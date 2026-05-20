<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class ScholarshipHolderDashboardService
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceSubmissionService $submissionService
    ) {}

    public function data(User $user, array $filters = [], ?int $projectId = null): array
    {
        $holder = $user->scholarshipHolder;
        abort_if(! $holder, 403);

        $projects = $holder->projects()->with('positions')->get();

        $activeProject = $projectId
            ? $projects->firstWhere('id', $projectId)
            : $projects->first();

        $activeProjectId = $activeProject?->id;

        if ($projectId && ! $projects->contains('id', $projectId)) {
            abort(403);
        }

        $now = now()->startOfMonth();

        [$oldestPeriod, $oldestYear] = $this->resolveBounds($holder->id, $now, $activeProjectId);

        $selectedPeriod = $this->resolveSelectedPeriod(
            $filters['month'] ?? null,
            $oldestPeriod,
            $now
        );

        $selectedYear = $this->resolveSelectedYear(
            $filters['year'] ?? null,
            $oldestYear,
            (int) $now->year
        );

        $notificacoesPendentes = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();


        $monthlyRecordsByProject = AttendanceRecord::query()
            ->selectRaw('project_id, COUNT(*) as records_count, COUNT(DISTINCT date) as worked_days_count, COALESCE(SUM(hours), 0) as hours_total')
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $selectedPeriod->year)
            ->whereMonth('date', $selectedPeriod->month)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        $monthlyFrequencyByProject = $projects->map(function ($project) use ($monthlyRecordsByProject, $holder) {
            $projectMetrics = $monthlyRecordsByProject->get($project->id);
            $monthlyLimit = (float) $this->attendanceService->getMonthlyLimit($holder, (int) $project->id);
            $hoursTotal = (float) ($projectMetrics->hours_total ?? 0);

            return [
                'project_id' => (int) $project->id,
                'project_name' => (string) $project->name,
                'records_count' => (int) ($projectMetrics->records_count ?? 0),
                'worked_days_count' => (int) ($projectMetrics->worked_days_count ?? 0),
                'hours_total' => $hoursTotal,
                'monthly_limit' => $monthlyLimit,
                'completion_percent' => $monthlyLimit > 0
                    ? min(100, round(($hoursTotal / $monthlyLimit) * 100, 1))
                    : 0,
            ];
        })->values();

        $monthlyFrequencySummary = [
            'records_count' => (int) $monthlyFrequencyByProject->sum('records_count'),
            'worked_days_count' => (int) $monthlyFrequencyByProject->sum('worked_days_count'),
            'hours_total' => (float) $monthlyFrequencyByProject->sum('hours_total'),
            'monthly_limit' => (float) $monthlyFrequencyByProject->sum('monthly_limit'),
        ];
        $monthlyFrequencySummary['completion_percent'] = $monthlyFrequencySummary['monthly_limit'] > 0
            ? min(100, round(($monthlyFrequencySummary['hours_total'] / $monthlyFrequencySummary['monthly_limit']) * 100, 1))
            : 0;
        $periodRecordsQuery = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($q) => $q->where('project_id', $activeProjectId))
            ->whereYear('date', $selectedPeriod->year)
            ->whereMonth('date', $selectedPeriod->month);

        $recordsCount = (clone $periodRecordsQuery)->count();
        $workedDaysCount = (int) ((clone $periodRecordsQuery)
            ->selectRaw('COUNT(DISTINCT date) as aggregate')
            ->value('aggregate') ?? 0);
        $recordsHours = (float) (clone $periodRecordsQuery)->sum('hours');
        $monthlyLimit = (float) $this->attendanceService->getMonthlyLimit($holder, $activeProjectId);
        $completionPercent = $monthlyLimit > 0
            ? min(100, round(($recordsHours / $monthlyLimit) * 100, 1))
            : 0;

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($q) => $q->where('project_id', $activeProjectId))
            ->where('year', $selectedPeriod->year)
            ->where('month', $selectedPeriod->month)
            ->latest()
            ->first();

        $yearlySubmissionsQuery = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $selectedYear)
            ->when($activeProjectId, fn ($q) => $q->where('project_id', $activeProjectId));

        $yearlySubmissions = (clone $yearlySubmissionsQuery)
            ->orderByDesc('month')
            ->get();

        $submissionCounts = [
            'approved' => (clone $yearlySubmissionsQuery)
                ->where('status', AttendanceSubmission::STATUS_APPROVED)
                ->count(),
            'submitted' => (clone $yearlySubmissionsQuery)
                ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
                ->count(),
            'rejected' => (clone $yearlySubmissionsQuery)
                ->where('status', AttendanceSubmission::STATUS_REJECTED)
                ->count(),
            'late' => (clone $yearlySubmissionsQuery)
                ->where('status', AttendanceSubmission::STATUS_LATE)
                ->count(),
            'total' => $yearlySubmissions->count(),
        ];

        $yearPaymentsQuery = Payment::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($q) => $q->where('project_id', $activeProjectId))
            ->where('year', $selectedYear);

        $paymentTotals = [
            'sent' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_SENT)
                ->sum('amount'),
            'paid' => (float) (clone $yearPaymentsQuery)
                ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_CONFIRMED])
                ->sum('amount'),
            'confirmed' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_CONFIRMED)
                ->sum('amount'),
            'waiting_confirmation' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_PAID)
                ->sum('amount'),
            'year_total' => (float) (clone $yearPaymentsQuery)->sum('amount'),
        ];

        $paymentCounts = [
            'sent' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_SENT)
                ->count(),
            'paid' => (clone $yearPaymentsQuery)
                ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_CONFIRMED])
                ->count(),
            'confirmed' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_CONFIRMED)
                ->count(),
            'waiting_confirmation' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_CONFIRMED)
                ->count(),
            'total' => (clone $yearPaymentsQuery)->count(),
        ];

        $periodPayment = Payment::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $selectedPeriod->year)
            ->where('month', $selectedPeriod->month)
            ->when($activeProjectId, fn ($q) => $q->where('project_id', $activeProjectId))
            ->latest('id')
            ->first();

        $recentPayments = (clone $yearPaymentsQuery)
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $positionId = $activeProject?->pivot->position_id;
        $hourlyRate = (float) ($activeProject?->positions
            ->firstWhere('id', $positionId)
            ?->pivot->hourly_rate ?? 0);

        $periodEstimatedValue = (float) (
            $periodPayment?->amount
            ?? $submission?->calculated_value
            ?? ($recordsHours * $hourlyRate)
        );

        $canCreateRecord = $this->submissionService->canCreateRecord(
            $holder,
            $selectedPeriod->year,
            $selectedPeriod->month,
            $activeProjectId
        );

        $previousPeriod = $selectedPeriod->copy()->subMonth();
        $nextPeriod = $selectedPeriod->copy()->addMonth();

        return [
            'recordsCount' => $recordsCount,
            'workedDaysCount' => $workedDaysCount,
            'recordsHours' => $recordsHours,
            'monthlyLimit' => $monthlyLimit,
            'completionPercent' => $completionPercent,
            'periodEstimatedValue' => $periodEstimatedValue,
            'submission' => $submission,
            'status' => $submission?->status ?? 'open',
            'user' => $user,
            'currentYear' => (int) $now->year,
            'currentMonth' => (int) $now->month,
            'selectedYear' => $selectedYear,
            'selectedPeriod' => $selectedPeriod,
            'monthInput' => $selectedPeriod->format('Y-m'),
            'oldestPeriod' => $oldestPeriod,
            'oldestYear' => $oldestYear,
            'previousPeriod' => $previousPeriod,
            'nextPeriod' => $nextPeriod,
            'canNavigatePrevPeriod' => $previousPeriod->greaterThanOrEqualTo($oldestPeriod),
            'canNavigateNextPeriod' => $nextPeriod->lessThanOrEqualTo($now),
            'canNavigatePrevYear' => ($selectedYear - 1) >= $oldestYear,
            'canNavigateNextYear' => ($selectedYear + 1) <= (int) $now->year,
            'scholarshipHolder' => $holder,
            'projects' => $projects,
            'activeProject' => $activeProject,
            'activeProjectId' => $activeProjectId,
            'canCreateRecord' => $canCreateRecord,
            'submissionCounts' => $submissionCounts,
            'lastSubmissions' => $yearlySubmissions,
            'periodPayment' => $periodPayment,
            'recentPayments' => $recentPayments,
            'paymentTotals' => $paymentTotals,
            'paymentCounts' => $paymentCounts,
            'notificacoesPendentes' => $notificacoesPendentes,
            'recentNotifications' => $recentNotifications,
            'monthlyFrequencyByProject' => $monthlyFrequencyByProject,
            'monthlyFrequencySummary' => $monthlyFrequencySummary,
        ];
    }

    protected function resolveBounds(int $holderId, Carbon $fallback, ?int $projectId = null): array
    {
        $candidates = collect();

        $oldestRecordDate = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holderId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->orderBy('date')
            ->value('date');

        if ($oldestRecordDate) {
            $candidates->push(Carbon::parse($oldestRecordDate)->startOfMonth());
        }

        $oldestSubmission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holderId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->orderBy('year')
            ->orderBy('month')
            ->first(['year', 'month']);

        if ($oldestSubmission) {
            $candidates->push(
                Carbon::create($oldestSubmission->year, $oldestSubmission->month, 1)->startOfMonth()
            );
        }

        $oldestPayment = Payment::query()
            ->where('scholarship_holder_id', $holderId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->orderBy('year')
            ->orderBy('month')
            ->first(['year', 'month']);

        if ($oldestPayment) {
            $candidates->push(
                Carbon::create($oldestPayment->year, $oldestPayment->month, 1)->startOfMonth()
            );
        }

        $oldestPeriod = $candidates
            ->sortBy(fn (Carbon $date) => $date->timestamp)
            ->first() ?? $fallback->copy();

        return [$oldestPeriod, (int) $oldestPeriod->year];
    }

    protected function resolveSelectedPeriod(
        mixed $month,
        Carbon $oldestPeriod,
        Carbon $currentPeriod
    ): Carbon {
        if (! is_string($month) || ! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $currentPeriod->copy();
        }

        [$year, $monthNumber] = array_map('intval', explode('-', $month));

        $selectedPeriod = Carbon::create($year, $monthNumber, 1)->startOfMonth();

        if ($selectedPeriod->lessThan($oldestPeriod)) {
            return $oldestPeriod->copy();
        }

        if ($selectedPeriod->greaterThan($currentPeriod)) {
            return $currentPeriod->copy();
        }

        return $selectedPeriod;
    }

    protected function resolveSelectedYear(
        mixed $year,
        int $oldestYear,
        int $currentYear
    ): int {
        $selectedYear = is_numeric($year) ? (int) $year : $currentYear;

        return max($oldestYear, min($selectedYear, $currentYear));
    }
}
