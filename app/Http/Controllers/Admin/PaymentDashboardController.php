<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $query = Payment::where('year', $year)->where('month', $month);

        // Filtros
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        // Cards
        $totalPaid = $query->where('status', 'paid')->sum('amount');
        $totalConfirmed = $query->where('status', 'confirmed')->sum('amount');
        $totalPending  = $query->where('status', 'sent_to_payment')->sum('amount');

        // Quantidades
        $countPaid = $query->where('status', 'paid')->count();
        $countPending = $query->where('status', 'sent_to_payment')->count();

        // Gráficos
        $chartByProject = $query->selectRaw('project_id, SUM(amount) as total')
            ->groupBy('project_id')
            ->with('project')
            ->get();

        $chartByUnit = $query->selectRaw('unit_id, SUM(amount) as total')
            ->groupBy('unit_id')
            ->with('unit')
            ->get();

        // Tabelas
        $latestPayments = Payment::where('status', 'paid')
            ->latest()
            ->limit(10)
            ->get();

        $pendingPayments = Payment::where('status', 'sent_to_payment')
            ->limit(10)
            ->get();

        $projects = Project::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('admin.payments.dashboard', compact(
            'month','year','projects','units',
            'totalPaid','totalConfirmed','totalPending',
            'countPaid','countPending',
            'chartByProject','chartByUnit',
            'latestPayments','pendingPayments'
        ));
    }
}
