<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceRecord;

// Controller para a área do usuário (pode ser o dashboard principal após o login)
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
            $notificacoesPendentes = Notification::where('read', false)->count();

            $counts = [
                'approved' => AttendanceRecord::where('status', 'approved')->count(),
                'pending'  => AttendanceRecord::where('status', 'pending')->count(),
                'late'     => AttendanceRecord::late()->count(),
                'rejected' => AttendanceRecord::where('status', 'rejected')->count(),
            ];

            $myPending = 0;
            if ($user->scholarshipHolder) {
                $myPending = AttendanceRecord::where('scholarship_holder_id', $user->scholarshipHolder->id)
                    ->where('status', 'pending')
                    ->count();
            }
            $labels = ['Aprovados', 'Pendentes', 'Atrasados', 'Rejeitados'];
            $data = [
                $counts['approved'],    
                $counts['pending'],
                $counts['late'],
                $counts['rejected'],
            ];

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard', compact('user', 'notificacoesPendentes', 'counts', 'myPending', 'labels', 'data'));
    }
}