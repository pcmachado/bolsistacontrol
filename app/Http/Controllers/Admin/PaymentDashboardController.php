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
use Illuminate\Support\Facades\Auth;

class PaymentDashboardController extends Controller
{
    public function index(Request $request, PaymentDashboardService $service)
    {
        $user = Auth::user();

        $monthInput = $request->input('month');
        $resolvedMonth = now()->month;
        $resolvedYear = now()->year;

        if (is_string($monthInput) && preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            [$resolvedYear, $resolvedMonth] = array_map('intval', explode('-', $monthInput));
        } else {
            $resolvedMonth = (int) ($request->month ?? $resolvedMonth);
            $resolvedYear = (int) ($request->year ?? $resolvedYear);
        }

        $filters = [
            'month' => $resolvedMonth,
            'year'  => $resolvedYear,
            'project_id' => $request->project_id,
            'unit_id'    => $request->unit_id,
        ];

        $data = $service->data($user, $filters);

        return view('admin.payments.dashboard', $data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'month'   => ['required', 'integer', 'between:1,12'],
            'year'    => ['required', 'integer', 'min:2020'],
        ]);

        $hasPending = Payment::query()
            ->where('unit_id', $data['unit_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->where('status', Payment::STATUS_SENT)
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Existem pagamentos pendentes.');
        }

        // 🔒 evita duplicidade
        $exists = FinancialClosure::query()
            ->where('unit_id', $data['unit_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()->with('warning', 'Período já está fechado.');
        }

        FinancialClosure::create([
            ...$data,
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Período fechado com sucesso.');
    }
}
