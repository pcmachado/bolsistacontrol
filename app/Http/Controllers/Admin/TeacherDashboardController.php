<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassSession;
use App\Models\ClassOffering;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(Request $request, User $teacher)
    {
        //dd($teacher);
        // Garantir role de professor
        if (!$teacher->hasRole('professor')) {
            abort(403, 'Este usuário não é um professor.');
        }

        // Base query das aulas
        $sessions = ClassSession::query()
            ->whereHas('classOffering.disciplines', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with(['discipline.course', 'classOffering.unit'])
            ->orderBy('date');

        // FILTROS
        if ($request->filled('filter_from')) {
            $sessions->whereDate('date', '>=', $request->filter_from);
        }
        if ($request->filled('filter_to')) {
            $sessions->whereDate('date', '<=', $request->filter_to);
        }
        if ($request->filled('filter_unit')) {
            $sessions->whereHas('classOffering', fn($q) =>
                $q->where('unit_id', $request->filter_unit)
            );
        }
        if ($request->filled('filter_course')) {
            $sessions->whereHas('discipline', fn($q) =>
                $q->where('course_id', $request->filter_course)
            );
        }

        $sessions = $sessions->get();

        // KPIs
        $totalHours = $sessions->sum('duration_hours');
        $totalClasses = $sessions->count();
        $totalCourses = $sessions->pluck('discipline.course.id')->unique()->count();
        $totalOfferings = $sessions->pluck('classOffering.id')->unique()->count();

        // Gráficos
        $hoursByMonth = $sessions->groupBy(fn($s) => $s->date->format('Y-m'))
            ->map(fn($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn($g) => [
                'label' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByOffering = $sessions->groupBy('class_offering_id')
            ->map(fn($g) => [
                'label' => optional($g->first()->classOffering)->name ?? "Turma #".$g->first()->classOffering->id,
                'hours' => $g->sum('duration_hours'),
            ]);

        // Aulas recentes
        $recent = $sessions->sortByDesc('date')->take(10);

        $classes = ClassOffering::whereHas('disciplines', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->with(['course', 'unit', 'disciplines' => fn($q) =>
            $q->where('teacher_id', $teacher->id)
        ])
        ->get();dd($classes);

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'sessions' => $sessions,
            'recent' => $recent,
            'totalHours' => $totalHours,
            'totalClasses' => $totalClasses,
            'totalCourses' => $totalCourses,
            'totalOfferings' => $totalOfferings,
            'hoursByMonth' => $hoursByMonth,
            'hoursByDiscipline' => $hoursByDiscipline,
            'hoursByOffering' => $hoursByOffering,
            'units' => \App\Models\Unit::all(),
            'courses' => \App\Models\Course::all(),
            'classes' => $classes
        ]);
    }
}
