<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceDashboardService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboard,
        protected AttendanceDashboardService $attendanceDashboard
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['month', 'start_date', 'end_date']);
        $general = $this->dashboard->getDashboardData($filters);
        $financial = $this->dashboard->getFinancialData($filters);

        return view('admin.dashboard', [
            ...$general,
            'financialOverview' => $financial,
            'attendanceCards' => $this->attendanceDashboard->submissionCounts(Auth::user()),
            'selectedMonthInput' => $request->filled('month') ? $request->input('month') : now()->format('Y-m'),
            'selectedStartDate' => $request->input('start_date'),
            'selectedEndDate' => $request->input('end_date'),
        ]);
    }

    public function stats(Request $request)
    {
        if (! $request->has('month') && ! ($request->has('start_date') && $request->has('end_date'))) {
            return response()->json(['error' => 'Parâmetros insuficientes'], 422);
        }

        return response()->json([
            'general' => $this->dashboard->getDashboardData($request->all()),
            'financial' => $this->dashboard->getFinancialData($request->all()),
            'attendance' => $this->attendanceDashboard->submissionCounts(Auth::user()),
        ]);
    }
}
