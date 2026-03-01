<?php

namespace App\Http\Controllers;

use App\Models\FinalActivityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FinalActivityReportController extends Controller
{
    public function create()
    {
        $this->authorize('create', FinalActivityReport::class);

        return view('attendance.reports.final.form');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        $data = $request->validate([
            'project_id'    => 'nullable|exists:projects,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date',
            'activities'    => 'required|string',
            'results'       => 'required|string',
            'contributions' => 'required|string',
            'status'        => FinalActivityReport::STATUS_DRAFT,
        ]);

        $report = FinalActivityReport::create([
            ...$data,
            'scholarship_holder_id' => $holder->id,
            'submitted_at'          => now(),
        ]);

        return redirect()
            ->route('attendance.reports.final.edit', $report)
            ->with('success', 'Relatório final criado com sucesso.');
    }

    public function edit(FinalActivityReport $report)
    {
        $this->authorize('update', $report);

        return view('attendance.reports.final.form', compact('report'));
    }

    public function update(Request $request, FinalActivityReport $report)
    {
        $this->authorize('update', $report);

        $data = $request->validate([
            'project_id'    => 'nullable|exists:projects,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date',
            'activities'    => 'required|string',
            'results'       => 'required|string',
            'contributions' => 'required|string',
        ]);

        $report->update($data);

        return back()->with('success', 'Relatório final atualizado com sucesso.');
    }
    
    public function submit(FinalActivityReport $report)
    {
        $this->authorize('submit', $report);

        $report->update([
            'status' => FinalActivityReport::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Relatório enviado para aprovação.');
    }

    public function approve(FinalActivityReport $report)
    {
        $this->authorize('approve', $report);

        $report->update([
            'status'       => FinalActivityReport::STATUS_APPROVED,
            'approved_at'  => now(),
            'approved_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Relatório aprovado.');
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

        return $pdf->download(
            'relatorio_final_'.$report->id.'.pdf'
        );
    }
}
