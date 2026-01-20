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
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;

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

        $data = $this->dashboard->getDashboardData($request->all());

        return view('admin.dashboard', $data);
    }

    /**
     * Endpoint para AJAX (gráfico e cards)
     */
    public function stats(Request $request, DashboardService $service)
    {
        // AJAX sempre envia month/year
        if (!$request->has('month')) {
            return response()->json(['error' => 'Parâmetros insuficientes'], 422);
        }

        return response()->json([
            'general'   => $service->getDashboardData($request->all()),
            'financial' => $service->getFinancialData($request->all()),
        ]);
    }

    /*public function stats(Request $request)
    {
        return response()->json([
            'ok' => true,
            'params' => $request->all(),
        ]);
    }*/

}