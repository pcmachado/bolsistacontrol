<?php

namespace App\Http\Controllers;

use App\Models\FinalActivityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FinalActivityReportController extends Controller
{
    private function getProjectContext()
    {
        $holder = Auth::user()->scholarshipHolder;

        $project = $holder?->projects()->first();
        $pivot   = $project?->pivot;

        return compact('holder', 'project', 'pivot');
    }

    public function index()
    {
        extract($this->getProjectContext());

        $report = FinalActivityReport::where('scholarship_holder_id', $holder?->id)
            ->latest()
            ->first();

        return view('attendance.reports.final.index', compact(
            'report',
            'project',
            'pivot'
        ));
    }

    public function create()
    {
        $this->authorize('create', FinalActivityReport::class);

        extract($this->getProjectContext());

        return view('attendance.reports.final.form', compact(
            'project',
            'pivot'
        ));
    }

    public function store(Request $request)
    {
    
        extract($this->getProjectContext());

        $data = $request->validate([
            'end_date'      => 'required|date',
            'activities'    => 'required|string',
            'results'       => 'required|string',
            'contributions' => 'required|string',
        ]);

        $data['start_date'] = $pivot?->start_date;
        $data['project_id'] = $project?->id;
        $data['status']     = FinalActivityReport::STATUS_DRAFT;

        $report = FinalActivityReport::create([
            ...$data,
            'scholarship_holder_id' => $holder->id,
        ]);

        return redirect()
            ->route('attendance.reports.final.show', $report)
            ->with('success', 'Relatório final criado com sucesso.');
    }

    public function edit(FinalActivityReport $report)
    {
        $this->authorize('update', $report);

        extract($this->getProjectContext());

        return view('attendance.reports.final.form', compact(
            'report',
            'project',
            'pivot'
        ));
    }

    public function update(Request $request, FinalActivityReport $report)
    {
        $this->authorize('update', $report);

        $data = $request->validate([
            'end_date'      => 'required|date',
            'activities'    => 'required|string',
            'results'       => 'required|string',
            'contributions' => 'required|string',
        ]);

        $report->update($data);

        return back()->with('success', 'Relatório final atualizado com sucesso.');
    }

    public function show(FinalActivityReport $report)
    {
        $this->authorize('view', $report);

        return view('attendance.reports.final.show', compact('report'));
    }

    public function pdf(FinalActivityReport $report)
    {
        $this->authorize('view', $report);

        $pdf = Pdf::loadView(
            'attendance.reports.final.pdf',
            compact('report')
        )->setPaper('a4', 'portrait');

        return $pdf->download("relatorio_final_{$report->id}.pdf");
    }

    public function blank()
    {
        $holder = Auth::user()->scholarshipHolder;
        $project = $holder?->projects()->first();
        $pivot = $project?->pivot;

        $pdf = Pdf::loadView('attendance.reports.final.blank', [
            'holder' => $holder,
            'project' => $project,
            'pivot' => $pivot,
            'isPdf' => false,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('relatorio_final_blank.pdf');
    }
}