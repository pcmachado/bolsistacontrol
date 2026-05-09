<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use App\Services\AcademicRiskService;
use Illuminate\Http\Request;

class AcademicDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Se for uma requisição específica para monitoramento de risco
        // if ($request->has('monitor_risk') || $request->route()->getName() === 'admin.dashboard.academic') {
        //     return $this->showRiskMonitoring($request);
        // }

        // FILTROS GLOBAIS
        $unitFilter = $request->filter_unit;
        $courseFilter = $request->filter_course;
        $teacherFilter = $request->filter_teacher;
        $from = $request->filter_from;
        $to = $request->filter_to;

        // BASE QUERY PARA AULAS
        $sessions = ClassSession::query()
            ->with(['teacher', 'discipline.course', 'classOffering.unit']);

        if ($unitFilter) {
            $sessions->whereHas('classOffering', fn ($q) => $q->where('unit_id', $unitFilter));
        }

        if ($courseFilter) {
            $sessions->whereHas('discipline', fn ($q) => $q->where('course_id', $courseFilter));
        }

        if ($teacherFilter) {
            $sessions->where('teacher_id', $teacherFilter);
        }

        if ($from) {
            $sessions->whereDate('date', '>=', $from);
        }

        if ($to) {
            $sessions->whereDate('date', '<=', $to);
        }

        $sessions = $sessions->get();

        // KPIs
        $totalHours = $sessions->sum('duration_hours');
        $totalClasses = $sessions->count();
        $totalTeachers = $sessions->pluck('teacher_id')->unique()->count();
        $totalCourses = $sessions->pluck('discipline.course_id')->unique()->count();

        // Contar turmas e bolsistas
        $totalOfferings = ClassOffering::count();
        $totalStudents = ScholarshipHolder::count();

        // Gráficos
        $hoursByMonth = $sessions->groupBy(fn ($s) => $s->date->format('Y-m'))
            ->map(fn ($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByCourse = $sessions->groupBy(fn ($s) => $s->discipline->course->id)
            ->map(fn ($g) => [
                'label' => $g->first()->discipline->course->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn ($g) => [
                'label' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $riskData = app(AcademicRiskService::class)->analyzeAll();

        $riskSummary = [
            'critical' => $riskData->where('level', 'critical')->count(),
            'risk' => $riskData->where('level', 'risk')->count(),
            'warning' => $riskData->where('level', 'warning')->count(),
        ];

        return view('admin.dashboard.academic.index', [
            'units' => Unit::all(),
            'courses' => Course::all(),
            'teachers' => User::role('professor')->get(),

            'totalHours' => $totalHours,
            'totalClasses' => $totalClasses,
            'totalTeachers' => $totalTeachers,
            'totalCourses' => $totalCourses,
            'totalOfferings' => $totalOfferings,
            'totalStudents' => $totalStudents,

            'hoursByMonth' => $hoursByMonth,
            'hoursByCourse' => $hoursByCourse,
            'hoursByTeacher' => $hoursByTeacher,
            'riskSummary' => $riskSummary,
        ]);
    }

    /**
     * Exibe monitor de risco com filtros e dados detalhados
     */
    private function showRiskMonitoring(Request $request)
    {
        $service = app(AcademicRiskService::class);
        $allRiskData = $service->analyzeAll();

        // Filtros
        $levelFilter = $request->get('level');
        $studentFilter = $request->get('student');

        // Aplicar filtro de nível
        if ($levelFilter) {
            $allRiskData = $allRiskData->filter(fn ($r) => $r['level'] === $levelFilter);
        }

        // Aplicar filtro de aluno
        if ($studentFilter) {
            $allRiskData = $allRiskData->filter(fn ($r) => stripos($r['student_name'] ?? '', $studentFilter) !== false ||
                stripos((string) $r['student_id'], $studentFilter) !== false
            );
        }

        // Resumo geral
        $allDataForSummary = $service->analyzeAll();
        $summary = [
            'critical' => $allDataForSummary->where('level', 'critical')->count(),
            'risk' => $allDataForSummary->where('level', 'risk')->count(),
            'warning' => $allDataForSummary->where('level', 'warning')->count(),
            'ok' => $allDataForSummary->where('level', 'ok')->count(),
        ];

        return view('admin.dashboard.academic.risk', [
            'data' => $allRiskData->values(),
            'summary' => $summary,
        ]);
    }

    public function risk(ClassOffering $offering)
    {
        $data = app(AcademicRiskService::class)->analyze($offering->id);

        return view('admin.dashboard.academic.risk', [
            'offering' => $offering,
            'data' => $data,
        ]);
    }
}
