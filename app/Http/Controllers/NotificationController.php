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
        $user = Auth::user();

        return view('notifications.index', [
            'notifications' => $user->notifications()->latest()->paginate(20),
            'unreadCount' => $user->unreadNotifications()->count()
        ]);
    }


    public function read($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }
// dd($notification->data);
        return redirect($notification->data['url'] ?? back());
    }

    public function markAll()
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Todas notificações foram lidas.');
    }
}
