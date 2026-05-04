<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdminDashboardService;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function __construct(
        protected SuperAdminDashboardService $service
    ) {}

    public function index(): View
    {
        return view('superadmin.dashboard', [
            'stats' => $this->service->getStats(),
            'institutions' => $this->service->institutions(),
            'recentUsers' => $this->service->recentUsers(),
        ]);
    }
}
