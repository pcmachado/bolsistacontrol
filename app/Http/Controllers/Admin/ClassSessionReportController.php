<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClassSessionsExport;

class ClassSessionReportController extends Controller
{
    // Relatório POR TURMA
    public function index(Request $request, ClassOffering $offering)
    {
        $sessions = ClassSession::with(['discipline', 'teacher'])
            ->where('class_offering_id', $offering->id)
            ->when($request->filled('filter_from'), fn($q) =>
                $q->whereDate('date', '>=', $request->filter_from))
            ->when($request->filled('filter_to'), fn($q) =>
                $q->whereDate('date', '<=', $request->filter_to))
            ->orderBy('date')
            ->get();

        // Totais
        $totalHours = $sessions->sum('duration_hours');

        // Agrupamentos
        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn($g) => [
                'discipline' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn($g) => [
                'teacher' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        return view('admin.class-offerings.sessions.report', [
            'offering' => $offering,
            'sessions' => $sessions,
            'totalHours' => $totalHours,
            'hoursByDiscipline' => $hoursByDiscipline,
            'hoursByTeacher' => $hoursByTeacher,
        ]);
    }

    // Relatório geral (opcional)
    public function global(Request $request)
    {
        // Podemos implementar depois
    }

    public function exportPdf(ClassOffering $offering)
    {
        $sessions = ClassSession::where('class_offering_id', $offering->id)
            ->with(['discipline', 'teacher'])
            ->orderBy('date')
            ->get();

        // Agrupar dados como no relatório original
        $totalHours = $sessions->sum('duration_hours');

        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn($g) => [
                'discipline' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn($g) => [
                'teacher' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        $pdf = Pdf::loadView('admin.class-offerings.sessions.report_pdf', [
            'offering' => $offering,
            'sessions' => $sessions,
            'totalHours' => $totalHours,
            'hoursByDiscipline' => $hoursByDiscipline,
            'hoursByTeacher' => $hoursByTeacher,
        ]);

        return $pdf->download("Relatorio_Aulas_{$offering->id}.pdf");
    }

    public function exportExcel(ClassOffering $offering)
    {
        return Excel::download(new ClassSessionsExport($offering),
            "Relatorio_Aulas_{$offering->id}.xlsx");
    }
}
