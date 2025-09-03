<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Notification;
use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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
        // Se precisar de dados específicos do bolsista logado
        // $bolsista = Bolsista::where('user_id', Auth::id())->first();
        // $notificacoesNaoLidas = $bolsista->notificacoes()->where('lida', false)->count();

        return view('dashboard');
    }
}