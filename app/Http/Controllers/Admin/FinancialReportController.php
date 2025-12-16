<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Unit;
use App\Models\ScholarshipHolder;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'month'   => $request->month,
            'year'    => $request->year,
            'project' => $request->project_id,
            'unit'    => $request->unit_id,
            'status'  => $request->status,
            'start'   => $request->start_date,
            'end'     => $request->end_date,
        ];

        $query = Payment::query()->with(['scholarshipHolder','project','unit']);

        // FILTROS -----------------------------
        if ($filters['month']) {
            $query->where('month', $filters['month']);
        }

        if ($filters['year']) {
            $query->where('year', $filters['year']);
        }

        if ($filters['project']) {
            $query->where('project_id', $filters['project']);
        }

        if ($filters['unit']) {
            $query->where('unit_id', $filters['unit']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['start']) {
            $query->whereDate('created_at', '>=', $filters['start']);
        }

        if ($filters['end']) {
            $query->whereDate('created_at', '<=', $filters['end']);
        }

        // RESULTADOS
        $payments = $query->orderBy('year')->orderBy('month')->get();

        $total = $payments->sum('amount');

        $projects = Project::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('admin.financial_reports.index', compact(
            'payments','total','projects','units','filters'
        ));
    }

    public function pdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = PDF::loadView('admin.financial_reports.pdf', $data);

        return $pdf->stream('relatorio_financeiro.pdf');
    }

    public function excel(Request $request)
    {
        return Excel::download(
            new FinancialReportExport($request),
            'relatorio_financeiro.xlsx'
        );
    }

    public function scholarshipHolder(Request $request)
    {
        $holders = ScholarshipHolder::with('user')->orderBy('name')->get();

        $selectedId = $request->scholarship_holder_id;

        $filters = [
            'year'    => $request->year,
            'project' => $request->project_id,
            'status'  => $request->status
        ];

        $payments = collect();

        if ($selectedId) {
            $payments = Payment::where('scholarship_holder_id', $selectedId)
                ->with(['project','unit'])
                ->orderBy('year')
                ->orderBy('month');

            // FILTROS
            if ($filters['year']) {
                $payments->where('year', $filters['year']);
            }
            if ($filters['project']) {
                $payments->where('project_id', $filters['project']);
            }
            if ($filters['status']) {
                $payments->where('status', $filters['status']);
            }

            $payments = $payments->get();
        }

        $projects = Project::orderBy('name')->get();

        return view('admin.financial_reports.scholarship_holder', compact(
            'holders', 'payments', 'projects',
            'selectedId', 'filters'
        ));
    }

    public function unitProject(Request $request)
    {
        $units = Unit::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $filters = [
            'unit_id'   => $request->unit_id,
            'project_id'=> $request->project_id,
            'year'      => $request->year,
            'month'     => $request->month,
            'status'    => $request->status,
        ];

        $query = Payment::with(['unit','project','scholarshipHolder']);

        // 🎯 FILTROS
        if ($filters['unit_id']) {
            $query->where('unit_id', $filters['unit_id']);
        }

        if ($filters['project_id']) {
            $query->where('project_id', $filters['project_id']);
        }

        if ($filters['year']) {
            $query->where('year', $filters['year']);
        }

        if ($filters['month']) {
            $query->where('month', $filters['month']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $payments = $query->orderBy('year')
                        ->orderBy('month')
                        ->get();

        // 📊 Totais agregados
        $totals = [
            'overall' => $payments->sum('amount'),
            'byUnit'  => $payments->groupBy('unit.name')
                                ->map(fn($c) => $c->sum('amount')),
            'byProject' => $payments->groupBy('project.name')
                                    ->map(fn($c) => $c->sum('amount')),
        ];

        return view('admin.financial_reports.unit_project', compact(
            'units','projects','payments','filters','totals'
        ));
    }

    public function institutional(Request $request)
    {
        $year = $request->year ?? now()->year;

        $payments = Payment::with(['unit','project','scholarshipHolder'])
            ->where('year', $year)
            ->get();

        // ======================
        // 🔹 AGREGADOS GERAIS
        // ======================

        $summary = [
            'total_paid'     => $payments->sum('amount'),
            'avg_monthly'    => $payments->groupBy('month')->map->sum('amount')->avg(),
            'active_units'   => $payments->groupBy('unit_id')->count(),
            'active_projects'=> $payments->groupBy('project_id')->count(),
            'active_bolsistas'=> $payments->groupBy('scholarship_holder_id')->count(),
        ];

        // ======================
        // 🔹 AGRUPAMENTOS
        // ======================

        $totals = [
            'byUnit' => $payments->groupBy(fn($p) => $p->unit->name)
                                ->map(fn($c) => $c->sum('amount')),
            'byProject' => $payments->groupBy(fn($p) => $p->project->name)
                                ->map(fn($c) => $c->sum('amount')),
            'byMonth' => $payments->groupBy('month')
                                ->map(fn($c) => $c->sum('amount')),
            'byStatus' => $payments->groupBy('status')
                                ->map(fn($c) => $c->sum('amount')),
        ];

        return view('admin.financial_reports.institutional', compact(
            'summary', 'totals', 'payments', 'year'
        ));
    }

}