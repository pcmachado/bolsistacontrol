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
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();

        $projectId = $request->input('project_id');

        $filters = $request->only(['month', 'start_date', 'end_date']);

        $projects = $user->unit?->isAdministrative()
            ? \App\Models\Project::all()
            : optional($user->scholarshipHolder)->projects()->get() ?? collect();

        if ($user->unit?->isAdministrative()) {
            $projects = \App\Models\Project::all();
        }

        $activeProject = $projectId
            ? $projects->firstWhere('id', $projectId)
            : $projects->first();

        $activeProjectId = $activeProject?->id;

        $data = $this->dashboard->getDashboardData(
            $filters,
            $activeProjectId
        );

        $financial = $this->dashboard->getFinancialData(
            $filters,
            $activeProjectId
        );

        return view('admin.dashboard', [
            ...$data,
            'financialOverview' => $financial,

            'projects' => $projects,
            'activeProject' => $activeProject,
            'activeProjectId' => $activeProjectId,

            'attendanceCards' => $this->attendanceDashboard->submissionCounts($user),

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

        $projectId = $request->input('project_id');

        return response()->json([
            'general' => $this->dashboard->getDashboardData($request->all(), $projectId),
            'financial' => $this->dashboard->getFinancialData($request->all(), $projectId),
            'attendance' => $this->attendanceDashboard->submissionCounts(Auth::user(), $projectId),
        ]);
    }
}
