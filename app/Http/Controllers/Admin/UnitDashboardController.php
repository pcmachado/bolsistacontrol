<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use Illuminate\Http\Request;

class UnitDashboardController extends Controller
{
    public function index(Request $request, Unit $unit)
    {
        // FILTROS
        $courseFilter = $request->filter_course;
        $teacherFilter = $request->filter_teacher;
        $offeringFilter = $request->filter_offering;
        $from = $request->filter_from;
        $to = $request->filter_to;

        // Base query de aulas filtrada pela unidade
        $sessions = ClassSession::query()
            ->whereHas('classOffering', fn($q) => $q->where('unit_id', $unit->id))
            ->with(['teacher', 'discipline.course', 'classOffering']);

        // Filtros opcionais
        if ($courseFilter) {
            $sessions->whereHas('discipline', fn($q) =>
                $q->where('course_id', $courseFilter)
            );
        }

        if ($teacherFilter) {
            $sessions->where('teacher_id', $teacherFilter);
        }

        if ($offeringFilter) {
            $sessions->where('class_offering_id', $offeringFilter);
        }

        if ($from) {
            $sessions->whereDate('date', '>=', $from);
        }

        if ($to) {
            $sessions->whereDate('date', '<=', $to);
        }

        $sessions = $sessions->orderBy('date')->get();

        // KPIs
        $totalHours = $sessions->sum('duration_hours');
        $totalClasses = $sessions->count();
        $totalTeachers = $sessions->pluck('teacher_id')->unique()->count();
        $totalCourses = $sessions->pluck('discipline.course_id')->unique()->count();

        // Contar turmas e bolsistas da unidade
        $totalOfferings = ClassOffering::where('unit_id', $unit->id)->count();
        $totalStudents = $unit->scholarshipHolders()->count() ?? 0;

        // Para gráficos
        $hoursByMonth = $sessions->groupBy(fn($s) => $s->date->format('Y-m'))
            ->map(fn($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByCourse = $sessions->groupBy(fn($s) => $s->discipline->course->id)
            ->map(fn($g) => [
                'label' => $g->first()->discipline->course->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn($g) => [
                'label' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByOffering = $sessions->groupBy('class_offering_id')
            ->map(fn($g) => [
                'label' => $g->first()->classOffering->name ?? "Turma ".$g->first()->classOffering->id,
                'hours' => $g->sum('duration_hours'),
            ]);

        return view('admin.dashboard.unit.index', [
            'unit' => $unit,
            'totalHours' => $totalHours,
            'totalClasses' => $totalClasses,
            'totalTeachers' => $totalTeachers,
            'totalCourses' => $totalCourses,
            'totalOfferings' => $totalOfferings,
            'totalStudents' => $totalStudents,
            'hoursByMonth' => $hoursByMonth,
            'hoursByCourse' => $hoursByCourse,
            'hoursByTeacher' => $hoursByTeacher,
            'hoursByOffering' => $hoursByOffering,
            'courses' => \App\Models\Course::all(),
            'teachers' => \App\Models\User::role('professor')->get(),
            'offerings' => ClassOffering::where('unit_id', $unit->id)->get(),
        ]);
    }
}
