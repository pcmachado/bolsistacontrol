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
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    public function markAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas as notificações foram marcadas como lidas!');
    }
}
