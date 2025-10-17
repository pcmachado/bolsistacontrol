<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homologation;
use App\Models\AttendanceRecord;
use App\Services\AttendanceRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\ReportExport;

class HomologationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = AttendanceRecord::where('status', 'submitted')
            ->with('scholarshipHolder.unit');

        // Coordenador Adjunto → só vê a própria unidade
        if (Auth::user()->hasRole('coordenador_adjunto')) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', Auth::user()->unit_id)
            );
        }

        // Coordenador Geral → pode filtrar qualquer unidade
        if ($request->filled('unit_id') && Auth::user()->hasRole('coordenador_geral')) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $request->unit_id)
            );
        }

        $records = $query->get();
        $units = \App\Models\Unit::all();

        return view('attendance.homologation.index', compact('records', 'units'));
    }

    public function approve(AttendanceRecord $record, AttendanceRecordService $service)
    {
        $this->authorize('approve', $record);
        $service->approveRecord($record);

        return back()->with('success', 'Registro homologado com sucesso.');
    }

    public function reject(Request $request, AttendanceRecord $record, AttendanceRecordService $service)
    {
        $this->authorize('reject', $record);
        $request->validate(['reason' => 'required|string']);
        $service->rejectRecord($record, $request->reason);

        return back()->with('success', 'Registro rejeitado e devolvido ao bolsista.');
    }

    public function report(Request $request, AttendanceRecordService $service)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        if (Auth::user()->hasRole('coordenador_adjunto')) {
            $unitId = Auth::user()->unit_id;
        } else {
            $unitId = $request->input('unit_id'); // pode ser null
        }

        $report = $service->generateReport($unitId, $month, $year);
        $units = \App\Models\Unit::all();

        // 🔑 Aqui entra a exportação
        if ($request->has('export')) {
            if ($request->export === 'pdf') {
                $pdf = \PDF::loadView('attendance.homologation.report_pdf', compact('report','month','year','unitId'));
                return $pdf->download("relatorio_frequencia_{$month}_{$year}.pdf");
            }
            if ($request->export === 'excel') {
                return \Excel::download(new \App\Exports\ReportExport($report, $month, $year, $unitId), "relatorio_frequencia_{$month}_{$year}.xlsx");
            }
        }

        return view('attendance.homologation.report', compact('report', 'month', 'year', 'units', 'unitId'));
    }

    public function bulk(Request $request, AttendanceRecordService $service)
    {
        $action = $request->input('action');
        $records = AttendanceRecord::whereIn('id', $request->input('records', []))->get();

        foreach ($records as $record) {
            if ($action === 'approve') {
                $service->approveRecord($record);
            } elseif ($action === 'reject') {
                $service->rejectRecord($record, $request->input('reason'));
            }
        }

        return response()->json(['success' => true]);
    }

}
