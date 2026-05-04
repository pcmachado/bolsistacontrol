<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index(Request $request, User $teacher)
    {
        // Garantir role de professor
        abort_unless(
            $teacher->canAccessTeacher(),
            403,
            'Este usuário não possui acesso como professor.'
        );

        // Base query das aulas
        $sessions = ClassSession::query()
            ->forTeacher($teacher->id)
            ->whereHas('classOffering', function ($q) use ($teacher) {
                $q->whereIn('id',
                    $teacher->assignments()
                        ->where('assignment_type', \App\Models\Assignment::TYPE_PROFESSOR)
                        ->pluck('class_offering_id')
                );
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
            $sessions->whereHas('classOffering', fn ($q) => $q->where('unit_id', $request->filter_unit)
            );
        }
        if ($request->filled('filter_course')) {
            $sessions->whereHas('discipline', fn ($q) => $q->where('course_id', $request->filter_course)
            );
        }

        $sessions = $sessions->get();

        // KPIs
        $totalHours = $sessions->sum('duration_hours');
        $totalClasses = $sessions->count();
        $totalCourses = $sessions->pluck('discipline.course.id')->unique()->count();
        $totalOfferings = $sessions->pluck('classOffering.id')->unique()->count();

        // Gráficos
        $hoursByMonth = $sessions->groupBy(fn ($s) => $s->date->format('Y-m'))
            ->map(fn ($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn ($g) => [
                'label' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        $hoursByOffering = $sessions->groupBy('class_offering_id')
            ->map(fn ($g) => [
                'label' => optional($g->first()->classOffering)->name ?? 'Turma #'.$g->first()->classOffering->id,
                'hours' => $g->sum('duration_hours'),
            ]);

        // Aulas recentes
        $recent = $sessions->sortByDesc('date')->take(10);

        $classes = ClassOffering::whereHas('disciplines', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
            ->with(['course', 'unit', 'disciplines' => fn ($q) => $q->where('teacher_id', $teacher->id),
            ])
            ->get();

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
            'units' => Auth::user()->assignments()
                ->pluck('unit_id')
                ->filter()
                ->unique()
                ->map(fn ($id) => \App\Models\Unit::find($id)),
            'courses' => \App\Models\Course::all(),
            'classes' => $classes,
        ]);
    }
}
