<?php
// app/Http/Controllers/Admin/DashboardController.php
// Controller para a área administrativa
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard da área administrativa.
     * Coleta dados de alto nível para uma visão geral do sistema.
     */
    public function index(): View
    {
        // Contagem de registros para o dashboard administrativo
        $totalBolsistas = ScholarshipHolder::count();
        $totalUnidades = Unit::count();
        $notificacoesPendentes = Notification::where('read', false)->count();

        // Passa os dados para a view
        return view('admin.dashboard', compact('totalBolsistas', 'totalUnidades', 'notificacoesPendentes'));
    }
}