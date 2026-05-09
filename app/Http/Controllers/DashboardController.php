<?php

namespace App\Http\Controllers;

use App\Services\ScholarshipHolderDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, ScholarshipHolderDashboardService $service)
    {
        $projectId = $request->input('project_id');
        $data = $service->data(Auth::user(), $request->only(['month', 'year']), $projectId);

        return view('dashboard', $data);
    }

    public function stats(Request $request)
    {
        $user = auth()->user();
        $projectId = $request->input('project_id');

        return response()->json([
            'data' => app(ScholarshipHolderDashboardService::class)
                ->data($user, $request->all(), $projectId),

            'attendance' => app(\App\Services\AttendanceDashboardService::class)
                ->submissionCounts($user, $projectId, 'self'),
        ]);
    }
}
