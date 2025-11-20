<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected DataScopeService $scopeService;

    public function __construct(DataScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function getDashboardData(array $filters): array
    {
        $user = Auth::user();
        $scope = $this->scopeService->getScope();

        // ================================
        // 1) FILTRO DE MÊS
        // ================================
        $month = $filters['month'] ?? now()->month;
        $year  = $filters['year']  ?? now()->year;

        $currentMonth = now()->format('Y-m');

        // ================================
        // 2) Consulta principal
        // ================================
        $query = AttendanceRecord::query()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->byUserScope($scope); // 🔥 Aqui aplicamos o escopo

        // ================================
        // 2.1) 🔒 Se for coordenador adjunto, filtra pela unidade dele
        // ================================
        $unitName = null;
        $role = auth()->user()->roles->pluck('name')->first();

        if ($role === 'coordenador_adjunto') {
            $unitId = auth()->user()->unit_id;
            $query->whereHas('scholarshipHolder', fn($q) => $q->where('unit_id', $unitId));
            $unitName = optional(auth()->user()->unit)->name;
        }

        // ================================
        // 3) Contagens
        // ================================
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
        $lastSubmissions = AttendanceRecord::with('scholarshipHolder.user')
            ->where('status', 'submitted')
            ->byUserScope($scope)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        $lastApprovals = AttendanceRecord::with('scholarshipHolder.user')
            ->where('status', 'approved')
            ->byUserScope($scope)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // ================================
        // 5) Meus pendentes
        // ================================
        $myPending = 0;
        if ($user->scholarshipHolder) {
            $myPending = AttendanceRecord::where('scholarship_holder_id', $user->scholarshipHolder->id)
                ->where('status', 'draft')
                ->count();
        }

        // ================================
        // 6) Cards adicionais (instituição / sistema)
        // ================================
        $totalBolsistas = User::role('bolsista')->count();
        $totalUnidades = Unit::count();
        $unidades = Unit::all();
        $usersCount = User::count();
        $scholarshipHoldersCount = ScholarshipHolder::count();
        $coursesCount = Course::count();
        $notificacoesPendentes = Notification::where('read', false)->count();

        // ================================
        // 7) Gráfico
        // ================================
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
            'scope',
            'lastSubmissions',
            'lastApprovals',
            'unitName',
            'currentMonth'
        );
    }
}
