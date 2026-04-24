<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder; 
use App\Models\Unit;
use App\Models\Course;
use App\Models\User;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Discipline;
use App\Models\ClassOffering;
use App\Models\AttendanceSubmission;
use App\Services\AttendanceDashboardService;
use App\Services\VisibilityService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification as Notification;

class DashboardService
{
    /* =========================================================
     |  DASHBOARD OPERACIONAL (FREQUÊNCIA)
     ========================================================= */
    public function getDashboardData(array $filters): array
    {
        $user = Auth::user();

        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);
        $currentMonth = now()->format('Y-m');

        $scope = $this->resolveScope($user);

        $unitName = $user->unit ? $user->unit->name : null;

        /* -------------------------------
         | Acadêmico (somente visão ampla)
         ------------------------------- */
        $academic = [];
        if (in_array($scope, ['all', 'institution'])) {
            $academic = [
                'projects_active'        => Project::where('status', 'active')->count(),
                'projects_draft'         => Project::where('status', 'draft')->count(),
                'courses_total'          => Course::count(),
                'disciplines_total'      => Discipline::count(),
                'class_offerings_active' => ClassOffering::count(),
            ];
        }

        $query = AttendanceSubmission::query()
            ->with('scholarshipHolder.user');

        // aplica o mesmo escopo do dashboard
        $query = app(VisibilityService::class)
            ->apply($query, $user, 'self');

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
                'attendance_submitted'  => $attendance['submitted'],
                'attendance_rejected' => $attendance['rejected'],
                'payments_pending_execution' =>
                    Payment::where('status', Payment::STATUS_SENT)->count(),
                'payments_waiting_confirmation' =>
                    Payment::where('status', Payment::STATUS_PAID)->count(),
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
            'approved'  => (clone $query)->where('status', AttendanceSubmission::STATUS_APPROVED)->count(),
            'submitted' => (clone $query)->where('status', AttendanceSubmission::STATUS_SUBMITTED)->count(),
            'rejected'  => (clone $query)->where('status', AttendanceSubmission::STATUS_REJECTED)->count(),
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
            'year'
        );
    }

    /* =========================================================
     |  DASHBOARD FINANCEIRO
     ========================================================= */
    public function getFinancialData(array $filters): array
    {
        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);

        $user  = Auth::user();
        $scope = $this->resolveScope($user);

        $query = Payment::query();

        if ($month) {
            $query->where('year', $year)->where('month', $month);
        } else {
            $query->whereBetween(
                DB::raw("DATE(CONCAT(year,'-',LPAD(month,2,'0'),'-01'))"),
                [$startDate->startOfMonth(), $endDate->endOfMonth()]
            );
        }

        $this->applyPaymentScope($query, $user, $scope);

        $counts = [
            'generated' => (clone $query)->count(),
            'sent'      => (clone $query)->where('status', Payment::STATUS_SENT)->count(),
            'paid'      => (clone $query)->where('status', Payment::STATUS_PAID)->count(),
            'confirmed' => (clone $query)->where('status', Payment::STATUS_CONFIRMED)->count(),
        ];

        $totals = [
            'sent'      => (clone $query)->where('status', Payment::STATUS_SENT)->sum('amount'),
            'paid'      => (clone $query)->where('status', Payment::STATUS_PAID)->sum('amount'),
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
            'institution' =>
                $query->whereHas('unit', fn ($q) =>
                    $q->where('institution_id', $user->institution_id)
                ),

            'unit' =>
                $query->where('unit_id', $user->unit_id),

            'self' =>
                $query->where('scholarship_holder_id', $user->scholarshipHolder->id),

            'none' =>
                $query->whereRaw('1 = 0'),

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
            $users->count(),
            $holders->count(),
            $units->count(),
            $units->get(),
        ];
    }

    private function resolvePeriod(array $filters): array
    {
        if (!empty($filters['month']) && preg_match('/^\d{4}-\d{2}$/', $filters['month'])) {
            [$year, $month] = explode('-', $filters['month']);
            return [
                (int)$year,
                (int)$month,
                Carbon::create($year, $month, 1)->startOfMonth(),
                Carbon::create($year, $month, 1)->endOfMonth(),
            ];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $start = Carbon::parse($filters['start_date'])->startOfDay();
            $end   = Carbon::parse($filters['end_date'])->endOfDay();
            return [(int)$start->year, null, $start, $end];
        }

        $now = now();
        return [(int)$now->year, (int)$now->month, $now->startOfMonth(), $now->endOfMonth()];
    }
}
