<?php

namespace App\Http\Controllers;

use App\Services\ScholarshipHolderDashboardService;
use App\Http\Controllers\Controller;
use App\Services\AttendanceDashboardService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboard,
        protected AttendanceDashboardService $attendanceDashboard
    ) {}

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

        $dashboard = app(ScholarshipHolderDashboardService::class)
            ->data(
                $user,
                $request->only(['month', 'year']),
                $projectId
            );

        return response()->json([

            'general' => [
                'recordsCount' => $dashboard['recordsCount'],
                'recordsHours' => $dashboard['recordsHours'],
                'workedDaysCount' => $dashboard['workedDaysCount'],
                'monthlyLimit' => $dashboard['monthlyLimit'],
                'completionPercent' => $dashboard['completionPercent'],
                'periodEstimatedValue' => $dashboard['periodEstimatedValue'],
            ],

            'financial' => [
                'periodPayment' => $dashboard['periodPayment'],

                'sent' => $dashboard['paymentTotals']['sent'] ?? 0,
                'paid' => $dashboard['paymentTotals']['paid'] ?? 0,
                'confirmed' => $dashboard['paymentTotals']['confirmed'] ?? 0,
                'waiting_confirmation' =>
                    $dashboard['paymentTotals']['waiting_confirmation'] ?? 0,
            ],

            'attendance' => $dashboard['submissionCounts'] ?? [],
        ]);
    }
}
