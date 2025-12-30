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

        $baseQuery = Payment::where('year', $year)
            ->where('month', $month);

        // 🔒 Escopo por papel (recomendado)
        if (auth()->user()->hasRole('coordenador_adjunto')) {
            $baseQuery->whereIn(
                'unit_id',
                auth()->user()->units->pluck('id')
            );
        }

        if ($request->filled('project_id')) {
            $baseQuery->where('project_id', $request->project_id);
        }

        if ($request->filled('unit_id')) {
            $baseQuery->where('unit_id', $request->unit_id);
        }

        // 🔹 Cards financeiros
        $totalPaid = (clone $baseQuery)
            ->where('status', Payment::STATUS_PAID)
            ->sum('amount');

        $totalConfirmed = (clone $baseQuery)
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('amount');

        $totalPending = (clone $baseQuery)
            ->where('status', Payment::STATUS_SENT)
            ->sum('amount');

        // 🔹 Quantidades
        $countPaid = (clone $baseQuery)
            ->where('status', Payment::STATUS_PAID)
            ->count();

        $countPending = (clone $baseQuery)
            ->where('status', Payment::STATUS_SENT)
            ->count();

        // 🔹 Gráficos
        $chartByProject = (clone $baseQuery)
            ->selectRaw('project_id, SUM(amount) as total')
            ->groupBy('project_id')
            ->with('project')
            ->get();

        $chartByUnit = (clone $baseQuery)
            ->selectRaw('unit_id, SUM(amount) as total')
            ->groupBy('unit_id')
            ->with('unit')
            ->get();

        // 🔹 Listas
        $latestPayments = (clone $baseQuery)
            ->where('status', Payment::STATUS_PAID)
            ->latest('paid_at')
            ->limit(10)
            ->get();

        $pendingPayments = (clone $baseQuery)
            ->where('status', Payment::STATUS_SENT)
            ->latest('sent_at')
            ->limit(10)
            ->get();

        return view('admin.payments.dashboard', [
            'month' => $month,
            'year'  => $year,
            'projects' => Project::orderBy('name')->get(),
            'units'    => Unit::orderBy('name')->get(),

            'totalPaid'      => $totalPaid,
            'totalConfirmed' => $totalConfirmed,
            'totalPending'   => $totalPending,
            'countPaid'      => $countPaid,
            'countPending'   => $countPending,

            'chartByProject' => $chartByProject,
            'chartByUnit'    => $chartByUnit,

            'latestPayments' => $latestPayments,
            'pendingPayments'=> $pendingPayments,
        ]);
    }

}
