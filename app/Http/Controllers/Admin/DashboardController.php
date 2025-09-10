<?php
// app/Http/Controllers/Admin/DashboardController.php
// Controller para a área administrativa
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceRecord;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard da área administrativa.
     * Coleta dados de alto nível para uma visão geral do sistema.
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
            $unidades = Unit::all();

            $labels = Unit::pluck('name');
            $data = Unit::withCount('scholarshipHolders')->pluck('scholarship_holders_count');
            $notificacoesPendentes = Notification::where('read', false)->count();

            // Passa os dados para a view
            return view('admin.dashboard', compact('totalBolsistas', 'totalUnidades', 'notificacoesPendentes', 'unidades', 'labels', 'data'));
        }

        // Se não for um coordenador, retorna o dashboard padrão para o bolsista
        return view('dashboard');
    }
}