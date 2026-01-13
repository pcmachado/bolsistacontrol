<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification as Notification;
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
        $notifications = auth()->user()
            ->notifications()               // relação nativa
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }


    public function read($id)
    {
        $n = auth()->user()->notifications()->findOrFail($id);
        $n->markAsRead();

        // Redirecionar para link se existir
        if (!empty($n->data['url'])) {
            return redirect($n->data['url']);
        }

        return back();
    }

    public function markAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas as notificações foram marcadas como lidas!');
    }
}
