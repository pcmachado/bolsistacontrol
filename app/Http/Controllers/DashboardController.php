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
    /**
     * Exibe o dashboard principal do usuário logado.
     * Este dashboard pode mostrar informações personalizadas
     * para o bolsista ou ter links rápidos para as principais
     * funcionalidades, como o registro de frequência.
     */
    public function index(): View
    {
        $user = Auth::user();
        // Verifica se o usuário tem o papel de coordenador e o redireciona para a view do admin
        if ($user->hasRole('coordenador_geral') || $user->hasRole('coordenador_adjunto')) {
            // Pega dados de resumo para o dashboard do admin, se necessário
            $totalBolsistas = User::role('bolsista')->count();
            //$registrosPendentes = AttendanceRecord::where('status', 'pendente')->count();
            $totalUnidades = Unit::count();
            $notificacoesPendentes = Notification::where('read', false)->count();

            // Passa os dados para a view
            return view('admin.dashboard', compact('totalBolsistas', 'totalUnidades', 'notificacoesPendentes'));
        }

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard');
    }
}