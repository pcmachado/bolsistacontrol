<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sch = $user->scholarshipHolder;

        // projeto do bolsista (se houver)
        $project = $sch?->projects()->first();

        // notificações do usuário
        $notificacoesPendentes = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
        ->latest()
        ->take(5)
        ->get();

        // consulta base
        $query = AttendanceRecord::where('scholarship_holder_id', $sch?->id);

        // contadores
        $counts = [
            'approved'  => (clone $query)->where('status', 'approved')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'late'      => (clone $query)->late()->count(),
            'rejected'  => (clone $query)->where('status', 'rejected')->count(),
            'draft'     => (clone $query)->where('status', 'draft')->count(),
        ];

        // gráfico
        $labels = ['Aprovados', 'Rascunhos', 'Atrasados', 'Rejeitados', 'Enviados'];
        $data = [
            $counts['approved'],
            $counts['draft'],
            $counts['late'],
            $counts['rejected'],
            $counts['submitted'],
        ];

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

        return view('dashboard', compact(
            'user',
            'sch',
            'project',
            'counts',
            'data',
            'labels',
            'notificacoesPendentes',
            'recentNotifications',
            'lastSubmissions',
            'lastApprovals',
        ));
    }
}
