<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClassSessionsExport;
use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ClassSessionReportController extends Controller
{
    // Relatório POR TURMA
    public function index(Request $request, ClassOffering $offering)
    {
        $sessions = ClassSession::with(['discipline', 'teacher'])
            ->where('class_offering_id', $offering->id)
            ->when($request->filled('filter_from'), fn ($q) => $q->whereDate('date', '>=', $request->filter_from))
            ->when($request->filled('filter_to'), fn ($q) => $q->whereDate('date', '<=', $request->filter_to))
            ->orderBy('date')
            ->get();

        // Totais
        $totalHours = $sessions->sum('duration_hours');

        // Agrupamentos
        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn ($g) => [
                'discipline' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn ($g) => [
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
        $user = Auth::user();

        $query = ClassSession::with([
            'discipline',
            'teacher',
            'classOffering.unit',
            'classOffering.course',
        ]);

        // filtros
        if ($request->filled('filter_from')) {
            $query->whereDate('date', '>=', $request->filter_from);
        }

        if ($request->filled('filter_to')) {
            $query->whereDate('date', '<=', $request->filter_to);
        }

        if ($request->filled('unit_id')) {
            $query->whereHas('classOffering', fn ($q) => $q->where('unit_id', $request->unit_id));
        }

        if ($request->filled('course_id')) {
            $query->whereHas('classOffering', fn ($q) => $q->where('course_id', $request->course_id));
        }

        /*
        |--------------------------------------------------------------------------
        | VISIBILIDADE
        |--------------------------------------------------------------------------
        */
        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, $user, 'admin');

        $sessions = $query->orderBy('date')->get();

        // totais
        $totalHours = $sessions->sum('duration_hours');
        $totalClasses = $sessions->count();

        // por turma
        $hoursByClass = $sessions->groupBy('class_offering_id')
            ->map(fn ($g) => [
                'class' => $g->first()->classOffering->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        // por disciplina
        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn ($g) => [
                'discipline' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        // por professor
        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn ($g) => [
                'teacher' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        return view('admin.class-offerings.sessions.global', compact(
            'sessions',
            'totalHours',
            'totalClasses',
            'hoursByClass',
            'hoursByDiscipline',
            'hoursByTeacher'
        ));
    }

    public function show(ClassOffering $offering) {}

    public function exportPdf(ClassOffering $offering)
    {
        $sessions = ClassSession::query()
            ->where('class_offering_id', $offering->id)
            ->with(['discipline', 'teacher'])
            ->orderBy('date')
            ->get();

        // Agrupar dados como no relatório original
        $totalHours = $sessions->sum('duration_hours');

        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn ($g) => [
                'discipline' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
                'count' => $g->count(),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn ($g) => [
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
