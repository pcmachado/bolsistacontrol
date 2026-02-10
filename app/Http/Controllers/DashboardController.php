<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSubmission;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Support\Facades\Auth;
use App\Services\ScholarshipHolderDashboardService;

class DashboardController extends Controller
{
    public function index(ScholarshipHolderDashboardService $service)
    {
        $data = $service->data(auth()->user());

        return view('dashboard', $data);
    }
}
