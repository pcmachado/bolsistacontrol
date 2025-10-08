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
    public function index(): View
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

            // Passa os dados para a view
            return view('admin.dashboard', compact('totalBolsistas', 'totalUnidades', 'notificacoesPendentes', 'unidades', 'labels', 'usersCount', 'scholarshipHoldersCount', 'coursesCount'));
        }

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard');
    }
}