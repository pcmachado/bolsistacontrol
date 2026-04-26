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
    ) {
    }

    public function data(User $user, array $filters = []): array
    {
        $holder = $user->scholarshipHolder;
        abort_if(! $holder, 403);

        $project = $holder->projects()->with('positions')->first();
        $now = now()->startOfMonth();

        [$oldestPeriod, $oldestYear] = $this->resolveBounds($holder->id, $now);

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

        $periodRecordsQuery = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $selectedPeriod->year)
            ->whereMonth('date', $selectedPeriod->month);

        $recordsCount = (clone $periodRecordsQuery)->count();
        $workedDaysCount = (int) ((clone $periodRecordsQuery)
            ->selectRaw('COUNT(DISTINCT date) as aggregate')
            ->value('aggregate') ?? 0);
        $recordsHours = (float) (clone $periodRecordsQuery)->sum('hours');
        $monthlyLimit = (float) $this->attendanceService->getMonthlyLimit($holder);
        $completionPercent = $monthlyLimit > 0
            ? min(100, round(($recordsHours / $monthlyLimit) * 100, 1))
            : 0;

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $selectedPeriod->year)
            ->where('month', $selectedPeriod->month)
            ->latest()
            ->first();

        $yearlySubmissionsQuery = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $selectedYear);

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
            ->where('year', $selectedYear);

        $paymentTotals = [
            'sent' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_SENT)
                ->sum('amount'),
            'paid' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_PAID)
                ->sum('amount'),
            'confirmed' => (float) (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_CONFIRMED)
                ->sum('amount'),
            'year_total' => (float) (clone $yearPaymentsQuery)->sum('amount'),
        ];

        $paymentCounts = [
            'sent' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_SENT)
                ->count(),
            'paid' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_PAID)
                ->count(),
            'confirmed' => (clone $yearPaymentsQuery)
                ->where('status', Payment::STATUS_CONFIRMED)
                ->count(),
            'total' => (clone $yearPaymentsQuery)->count(),
        ];

        $periodPayment = Payment::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $selectedPeriod->year)
            ->where('month', $selectedPeriod->month)
            ->latest('id')
            ->first();

        $recentPayments = (clone $yearPaymentsQuery)
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $positionId = $project?->pivot->position_id;
        $hourlyRate = (float) ($project?->positions
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
            $selectedPeriod->month
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
            'project' => $project,
            'canCreateRecord' => $canCreateRecord,
            'submissionCounts' => $submissionCounts,
            'lastSubmissions' => $yearlySubmissions,
            'periodPayment' => $periodPayment,
            'recentPayments' => $recentPayments,
            'paymentTotals' => $paymentTotals,
            'paymentCounts' => $paymentCounts,
            'notificacoesPendentes' => $notificacoesPendentes,
            'recentNotifications' => $recentNotifications,
        ];
    }

    protected function resolveBounds(int $holderId, Carbon $fallback): array
    {
        $candidates = collect();

        $oldestRecordDate = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holderId)
            ->orderBy('date')
            ->value('date');

        if ($oldestRecordDate) {
            $candidates->push(Carbon::parse($oldestRecordDate)->startOfMonth());
        }

        $oldestSubmission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holderId)
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
