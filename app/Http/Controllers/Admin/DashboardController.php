<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use Illuminate\Notifications\DatabaseNotification as Notification;
use App\Models\Unit;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Services\DashboardService;
use App\Services\AttendanceDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboard;
    protected AttendanceDashboardService $attendanceDashboard;

    public function __construct(DashboardService $dashboard, AttendanceDashboardService $attendanceDashboard)
    {
        $this->dashboard = $dashboard;
        $this->attendanceDashboard = $attendanceDashboard;
    }

        /**
     * Dashboard principal com carregamento inicial
     */
    public function index(Request $request): View
    {
        // Mês atual por padrão
        if (!$request->has('month')) {
            $request->merge([
                'month' => now()->month,
                'year'  => now()->year,
            ]);
        }

        return view('admin.dashboard', [
            // 🔹 dados gerais / acadêmicos
            ...$this->dashboard->getDashboardData($request->all()),

            // 🔹 cards de frequência
            'attendanceCards' => $this->attendanceDashboard
                ->submissionCounts(auth()->user()),
        ]);

    }

    /**
     * Endpoint para AJAX (gráfico e cards)
     */
    public function stats(Request $request)
    {
        if (! $request->has('month')) {
            return response()->json(['error' => 'Parâmetros insuficientes'], 422);
        }

        return response()->json([
            // 🔹 dados gerais
            'general' => $this->dashboard->getDashboardData($request->all()),

            // 🔹 financeiro
            'financial' => $this->dashboard->getFinancialData($request->all()),

            // 🔹 frequência (submissões)
            'attendance' => $this->attendanceDashboard->submissionCounts(auth()->user()),
        ]);
    }

}