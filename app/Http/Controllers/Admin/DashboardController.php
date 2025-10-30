<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        // Verifica se o usuário tem o papel de coordenador e o redireciona para a view do admin
        if ($user->hasRole(['coordenador_geral', 'coordenador_adjunto'])) {
            // Pega dados de resumo para o dashboard do admin, se necessário
            $totalBolsistas = User::role('bolsista')->count();
            //$registrosPendentes = AttendanceRecord::where('status', 'pendente')->count();
            $totalUnidades = Unit::count();
            $unidades = Unit::all();

            $usersCount = User::count();
            $scholarshipHoldersCount = ScholarshipHolder::count();
            $coursesCount = Course::count();

            $labels = Unit::pluck('name');
            $notificacoesPendentes = Notification::where('read', false)->count();

            $month = $request->input('month', now()->month);
            $year  = $request->input('year', now()->year);

            $currentMonth = now()->format('Y-m');

            $query = AttendanceRecord::query()
                ->whereYear('date', $year)
                ->whereMonth('date', $month);

            $unitName = null;
            $role = auth()->user()->roles->pluck('name')->first();

            // 🔒 Se for coordenador adjunto, filtra pela unidade dele
            if ($role === 'coordenador_adjunto') {
                $unitId = auth()->user()->unit_id;
                $query->whereHas('scholarshipHolder', fn($q) => $q->where('unit_id', $unitId));
                $unitName = optional(auth()->user()->unit)->name;
            }

            $total = $query->count();

            $counts = [
                'approved'  => (clone $query)->where('status', 'approved')->count(),
                'submitted' => (clone $query)->where('status', 'submitted')->count(),
                'rejected'  => (clone $query)->where('status', 'rejected')->count(),
                'draft'     => (clone $query)->where('status', 'draft')->count(),
                'late'      => (clone $query)->where('status', 'late')->count(),
            ];

            $myPending = 0;
            if ($user->scholarshipHolder) {
                $myPending = AttendanceRecord::where('scholarship_holder_id', $user->scholarshipHolder->id)
                    ->where('status', 'draft')
                    ->count();
            }

            $labels = ['Aprovados', 'Enviados', 'Rejeitados', 'Rascunhos', 'Atrasados'];

            $data = [
                $counts['approved'],
                $counts['submitted'],
                $counts['rejected'],
                $counts['draft'],
                $counts['late']
            ];

            $percentages = collect($counts)->map(fn($c) => $total ? round($c / $total * 100, 1) : 0);

            $lastSubmissions = AttendanceRecord::with('scholarshipHolder.user')
                ->where('status', 'submitted')
                ->selectRaw('scholarship_holder_id, DATE(date) as date')
                ->groupBy('scholarship_holder_id', 'date')
                ->latest('date')
                ->take(5)
                ->get();

            $lastApprovals = AttendanceRecord::with('scholarshipHolder.user')
                ->where('status', 'approved')
                ->selectRaw('scholarship_holder_id, DATE(date) as date')
                ->groupBy('scholarship_holder_id', 'date')
                ->latest('date')
                ->take(5)
                ->get();

            // Passa os dados para a view
            return view('admin.dashboard', compact('totalBolsistas', 
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
                                                    'notificacoesPendentes',
                                                    'percentages', 
                                                    'month', 
                                                    'year', 
                                                    'unitName', 
                                                    'role', 
                                                    'lastSubmissions', 
                                                    'lastApprovals',
                                                    'currentMonth'
                                                ));
        }

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard');
    }

    public function stats(Request $request)
    {
        $query = AttendanceRecord::query();
        // 🔹 Se veio um mês (ex: 2025-10)
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // 🔹 Se veio intervalo de datas
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // 🔒 Se for coordenador adjunto, filtra pela unidade
        if (auth()->user()->hasRole('coordenador_adjunto')) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', auth()->user()->unit_id)
            );
        }

        $total = $query->count();

        $counts = [
            'approved'  => (clone $query)->where('status', 'approved')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'rejected'  => (clone $query)->where('status', 'rejected')->count(),
            'draft'     => (clone $query)->where('status', 'draft')->count(),
            'late'      => (clone $query)->where('status', 'late')->count(),
        ];

        $percentages = collect($counts)->map(fn($c) => $total ? round($c / $total * 100, 1) : 0);

        return response()->json([
            'counts' => $counts,
            'percentages' => $percentages,
            'total' => $total,
        ]);
    }

}