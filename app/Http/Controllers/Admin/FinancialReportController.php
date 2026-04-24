<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Unit;
use App\Models\ScholarshipHolder;
use App\DataTables\PaymentReportDataTable;
use App\Exports\FinancialReportExport;
use App\Services\FinancialReportService;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialReportController extends Controller
{
    public function index(Request $request, PaymentReportDataTable $dataTable, FinancialReportService $service)
    {
        $filters = $request->only([
            'month',
            'year',
            'project_id',
            'unit_id',
            'status',
            'start_date',
            'end_date',
        ]);

        if (empty($filters['month'])) {
            $filters['month'] = now()->format('Y-m');
        }

        $payments = $service->get($filters);
        $total = $payments->sum('amount');

        $projects = Project::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return $dataTable->setFilters($filters)
            ->render('admin.financial_reports.index', compact(
                'projects', 'units', 'filters', 'total', 'payments'
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

    protected function getReportData(Request $request): array
    {
        $query = Payment::with(['unit','project','scholarshipHolder']);

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->get();

        return [
            'payments' => $payments,
            'total' => $payments->sum('amount'),
        ];
    }

    public function scholarshipHolderPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView(
            'admin.financial_reports.scholarship_holder_pdf',
            $data
        );

        return $pdf->stream('relatorio_bolsista.pdf');
    }

    public function unitProjectPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView(
            'admin.financial_reports.unit_project',
            $data
        );

        return $pdf->stream('relatorio_unidade_projeto.pdf');
    }

    public function institutionalPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView(
            'admin.financial_reports.institutional',
            $data
        );

        return $pdf->stream('relatorio_institucional.pdf');
    }

    public function institutionalExcel(Request $request)
    {
        return Excel::download(
            new FinancialReportExport($request),
            'institucional.xlsx'
        );
    }

    public function scholarshipHolderExcel(Request $request)
    {
        return Excel::download(
            new FinancialReportExport($request),
            'bolsista.xlsx'
        );
    }

    public function unitProjectExcel(Request $request)
    {
        return Excel::download(
            new FinancialReportExport($request),
            'unidade_projeto.xlsx'
        );
    }

}