<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder; 
use App\Models\Unit;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Discipline;
use App\Models\ClassOffering;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification as Notification;

class DashboardService
{
    public function getDashboardData(array $filters): array
    {
        $user = Auth::user();

        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);

        $currentMonth = now()->format('Y-m');

        $query = AttendanceRecord::query()
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        $role = $user->roles->pluck('name')->first();
        $unitName = null;

        // superAdmin → vê tudo
        if ($user->hasRole('superAdmin')) {
            // sem filtro extra
        }
        // admin / coordenador_geral → vê todos os registros do sistema
        elseif ($user->hasRole('admin') || $user->hasRole('coordenador_geral') || $user->hasRole('coordenador_adjunto_geral')) {
            // sem filtro extra
        }
        // coordenador_adjunto / supervisor / apoio_administrativo / orientador → só unidade
        elseif (
            $user->hasRole('coordenador_adjunto') ||
            $user->hasRole('supervisor') ||
            $user->hasRole('apoio_administrativo') ||
            $user->hasRole('orientador')
        ) {
            $unitId = $user->unit_id;

            if ($unitId) {
                $query->whereHas('scholarshipHolder', function ($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                });

                $unitName = optional($user->unit)->name;
            } else {
                // sem unidade vinculada → não mostra nada
                $query->whereRaw('1 = 0');
            }
        }
        // bolsista → só os próprios registros
        elseif ($user->hasRole('bolsista')) {
            if ($user->scholarshipHolder) {
                $query->where('scholarship_holder_id', $user->scholarshipHolder->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // qualquer outro papel inesperado → vazio
        else {
            $query->whereRaw('1 = 0');
        }

        $total = $query->count();

        $counts = [
            'approved'  => (clone $query)->where('status', 'approved')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'rejected'  => (clone $query)->where('status', 'rejected')->count(),
            'draft'     => (clone $query)->where('status', 'draft')->count(),
            'late'      => (clone $query)->where('status', 'late')->count(),
        ];

        // ================================
        // Dados acadêmicos (só para admins)
        $academic = [];

        if ($user->hasAnyRole(['superAdmin', 'admin', 'coordenador_geral', 'coordenador_adjunto_geral'])) {

            $academic['projects_active'] = Project::where('status', 'active')->count();

            $academic['projects_draft'] = Project::where('status', 'draft')->count();

            $academic['courses_total'] = Course::count();

            $academic['disciplines_total'] = Discipline::count();
            $academic['class_offerings_active'] = ClassOffering::count();
        }

        // ================================
        // Alertas (só para admins)
        $alerts = [];

        if ($user->hasAnyRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral'])) {

            $alerts['attendance_pending'] = $counts['submitted'];
            $alerts['attendance_rejected'] = $counts['rejected'];

            $alerts['payments_pending_execution'] = $financial['payments_sent'] ?? 0;
            $alerts['payments_waiting_confirmation'] =
                Payment::where('status', Payment::STATUS_PAID)->count();
        }

        // ================================
        // Últimos eventos
        // ================================
        $lastSubmissions = (clone $query)
            ->with('scholarshipHolder.user')
            ->where('status', 'submitted')
            ->selectRaw('scholarship_holder_id, DATE(date) as date')
            ->groupBy('scholarship_holder_id', 'date')
            ->latest('date')
            ->take(5)
            ->get();

        $lastApprovals = (clone $query)
            ->with('scholarshipHolder.user')
            ->where('status', 'approved')
            ->selectRaw('scholarship_holder_id, DATE(date) as date')
            ->groupBy('scholarship_holder_id', 'date')
            ->latest('date')
            ->take(5)
            ->get();

        // ================================
        // 5) Meus pendentes
        // ================================
        $myPending = 0;
        if ($user->scholarshipHolder) {
            $myPending = AttendanceRecord::query()
                ->where('scholarship_holder_id', $user->scholarshipHolder->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'draft')
                ->count();
        }

        $baseUserQuery        = User::query();
        $baseScholarQuery     = ScholarshipHolder::query();
        $baseUnitQuery        = Unit::query();
        $baseCourseQuery      = Course::query();

        // Se quiser restringir para alguns papéis à unidade:
        if (
            $user->hasRole('coordenador_adjunto') ||
            $user->hasRole('supervisor') ||
            $user->hasRole('apoio_administrativo') ||
            $user->hasRole('orientador')
        ) {
            $unitId = $user->unit_id;

            if ($unitId) {
                $baseUserQuery->where('unit_id', $unitId);
                $baseScholarQuery->where('unit_id', $unitId);
                $baseUnitQuery->where('id', $unitId);
            }
        } elseif ($user->hasRole('bolsista')) {
            if ($user->scholarshipHolder) {
                $baseUserQuery->where('id', $user->id);
                $baseScholarQuery->where('id', $user->scholarshipHolder->id);
                $baseUnitQuery->where('id', $user->scholarshipHolder->unit_id);
            } else {
                $baseUserQuery->where('id', $user->id);
                $baseScholarQuery->whereRaw('1 = 0');
                $baseUnitQuery->whereRaw('1 = 0');
            }
        }
        // superAdmin / admin / coordenador_geral → veem tudo

        $usersCount              = $baseUserQuery->count();
        $scholarshipHoldersCount = $baseScholarQuery->count();
        $coursesCount            = $baseCourseQuery->count();
        $totalBolsistas          = $scholarshipHoldersCount;
        $totalUnidades           = $baseUnitQuery->count();
        $unidades                = $baseUnitQuery->get();

        // Notificações — por enquanto: todas não lidas (podemos depois filtrar por user)
        $notificacoesPendentes = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        $lastNotifications = $user->notifications()
            ->take(5)
            ->get();
        
        $percentages = collect($counts)->map(
            fn($c) => $total ? round($c / $total * 100, 1) : 0
        );

        $labels = ['Aprovados', 'Enviados', 'Rejeitados', 'Rascunhos', 'Atrasados'];

        $data = [
            $counts['approved'],
            $counts['submitted'],
            $counts['rejected'],
            $counts['draft'],
            $counts['late'],
        ];

        // ================================
        // 8) Retorno final
        // ================================
        return compact(
            'totalBolsistas',
            'totalUnidades',
            'notificacoesPendentes',
            'unidades',
            'labels',
            'usersCount',
            'scholarshipHoldersCount',
            'coursesCount',
            'data',
            'myPending',
            'counts',
            'user',
            'role',
            'percentages',
            'month',
            'year',
            'lastSubmissions',
            'lastApprovals',
            'unitName',
            'currentMonth',
            'recentNotifications',
            'academic',
            'alerts',
            'lastNotifications',
        );
    }

    public function getFinancialData(array $filters): array
    {
        $user = auth()->user();

        // período (mesma lógica que você já usa)
        [$year, $month, $startDate, $endDate] = $this->resolvePeriod($filters);

        $query = Payment::query();

        // 🔐 Escopo por papel
        if ($user->hasRole('coordenador_adjunto')) {
            $query->where('unit_id', $user->unit_id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('paid_at', [$startDate, $endDate]);
        } else {
            $query->where('month', $month)->where('year', $year);
        }

        return [
            'counts' => [
                'generated'  => (clone $query)->where('status', 'sent_to_payment')->count(),
                'paid'       => (clone $query)->where('status', 'paid')->count(),
                'confirmed'  => (clone $query)->where('status', 'confirmed')->count(),
            ],
            'totals' => [
                'generated'  => (clone $query)->where('status', 'sent_to_payment')->sum('amount'),
                'paid'       => (clone $query)->where('status', 'paid')->sum('amount'),
                'confirmed'  => (clone $query)->where('status', 'confirmed')->sum('amount'),
            ],
            'latest' => (clone $query)
                ->with('scholarshipHolder.user')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function resolvePeriod(array $filters): array
    {
        $year = null;
        $month = null;
        $startDate = null;
        $endDate = null;

        // Caso 1: input type="month" (YYYY-MM)
        if (!empty($filters['month']) && preg_match('/^\d{4}-\d{2}$/', $filters['month'])) {
            [$year, $month] = explode('-', $filters['month']);
            $year  = (int) $year;
            $month = (int) $month;

            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endDate   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
        }

        // Caso 2: intervalo manual
        elseif (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($filters['start_date'])->startOfDay();
            $endDate   = \Carbon\Carbon::parse($filters['end_date'])->endOfDay();

            $year  = (int) $startDate->year;
            $month = null; // não faz sentido em range
        }

        // Caso 3: fallback (mês atual)
        else {
            $now = now();
            $year  = $now->year;
            $month = $now->month;

            $startDate = $now->copy()->startOfMonth();
            $endDate   = $now->copy()->endOfMonth();
        }

        return [$year, $month, $startDate, $endDate];
    }

}
