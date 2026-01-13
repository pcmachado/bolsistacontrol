<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\User;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use Illuminate\Http\Request;

class GlobalDashboardController extends Controller
{
    public function index(Request $request)
    {
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
            $sessions->whereHas('classOffering', fn($q) =>
                $q->where('unit_id', $unitFilter));
        }

        if ($courseFilter) {
            $sessions->whereHas('discipline', fn($q) =>
                $q->where('course_id', $courseFilter));
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
        ]);
    }
}
