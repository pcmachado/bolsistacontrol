<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSubmission;
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
        $query = AttendanceSubmission::where('scholarship_holder_id', $sch?->id);

        // contadores
        $counts = [
            'approved'  => (clone $query)->where('status', 'approved')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'rejected'  => (clone $query)->where('status', 'rejected')->count(),
        ];

        // gráfico
        $labels = ['Aprovados', 'Rejeitados', 'Enviados'];
        $data = [
            $counts['approved'],
            $counts['rejected'],
            $counts['submitted'],
        ];

        $lastSubmissions = (clone $query)
            ->with('scholarshipHolder.user')
            ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
            ->selectRaw('scholarship_holder_id, DATE(date) as date')
            ->groupBy('scholarship_holder_id', 'date')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $lastApprovals = (clone $query)
            ->with('scholarshipHolder.user')
            ->where('status', AttendanceSubmission::STATUS_APPROVED)
            ->selectRaw('scholarship_holder_id, DATE(date) as date')
            ->groupBy('scholarship_holder_id', 'date')
            ->latest('approved_at')
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
