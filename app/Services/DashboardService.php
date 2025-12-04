<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder; 
use App\Models\Unit;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification as Notification;

class DashboardService
{
    public function getDashboardData(array $filters): array
    {
        $user = Auth::user();

        $monthInput = $filters['month'] ?? null;

        if ($monthInput && preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            // formato vindo do <input type="month">
            [$year, $month] = explode('-', $monthInput);
            $year  = (int) $year;
            $month = (int) $month;
        } else {
            // fallback: mês e ano separados
            $year  = isset($filters['year']) ? (int) $filters['year'] : (int) now()->format('Y');
            $month = isset($filters['month']) && is_numeric($filters['month'])
                ? (int) $filters['month']
                : (int) now()->format('m');
        }

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
        elseif ($user->hasRole('admin') || $user->hasRole('coordenador_geral')) {
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
        // 4) Últimos eventos
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
            'recentNotifications'
        );
    }
}
