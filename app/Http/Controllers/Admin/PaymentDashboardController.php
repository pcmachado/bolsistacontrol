<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\FinancialAlertService;
use App\Models\FinancialClosure;
use App\Services\PaymentDashboardService;

class PaymentDashboardController extends Controller
{
    public function index(Request $request, PaymentDashboardService $service)
    {
        $user = auth()->user();

        $filters = [
            'month' => $request->month ?? now()->month,
            'year'  => $request->year  ?? now()->year,
            'project_id' => $request->project_id,
            'unit_id'    => $request->unit_id,
        ];

        $data = $service->data($user, $filters);

        return view('admin.payments.dashboard', $data);
    }
}
