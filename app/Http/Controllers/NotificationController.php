<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\ScholarshipHolder;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Position;
use App\Models\User;
use App\Http\Controllers\ScholarshipHolderController;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $notificacoes = collect();
        if ($user != null && $user->role === 'coordenador') {
            $notificacoes = Notification::with('scholarshipHolder')->where('read', false)->latest()->get();
        } else {
            //$bolsista = ScholarshipHolder::where('user_id', $user->id)->first();
            // if ($bolsista) {
                //$notificacoes = $bolsista->notifications()->where('read', false)->latest()->get();
            // }
        }
        return view('notifications.index', compact('notificacoes'));
    }

    public function marcarLida(Notification $notificacao)
    {
        // Adicione uma verificação de permissão aqui
        $notificacao->update(['read' => true]);
        return back()->with('success', 'Notificação marcada como lida!');
    }
}
