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
                'submitted'  => AttendanceRecord::where('status', 'submitted')->count(),
                'late'     => AttendanceRecord::late()->count(),
                'rejected' => AttendanceRecord::where('status', 'rejected')->count(),
                'draft'    => AttendanceRecord::where('status', 'draft')->count(),
            ];

            $myPending = 0;
            if ($user->scholarshipHolder) {
                $myPending = AttendanceRecord::where('scholarship_holder_id', $user->scholarshipHolder->id)
                    ->where('status', 'draft')
                    ->count();
            }
            $labels = ['Aprovados', 'Rascunhos', 'Atrasados', 'Rejeitados', 'Enviados'];
            $data = [
                $counts['approved'],    
                $counts['draft'],
                $counts['late'],
                $counts['rejected'],
                $counts['submitted'],
            ];

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard', compact('user', 'notificacoesPendentes', 'counts', 'myPending', 'labels', 'data'));
    }
}