<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $teacher = $user->scholarshipHolder;

        abort_unless(
            $user->canAccessTeacher(),
            403,
            'Este usuário não possui acesso como professor.'
        );

        // IDs das turmas onde o usuário é professor
        $classIds = \App\Models\ClassOfferingDiscipline::query()
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->pluck('class_offering_id')
            ->unique();

        // Query base
        $sessions = ClassSession::query()
            ->whereHas('classOffering', fn ($q) => $q->whereIn('id', $classIds)
            )
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
                'label' => optional($g->first()->classOffering)->name
                    ?? 'Turma #'.$g->first()->classOffering->id,
                'hours' => $g->sum('duration_hours'),
            ]);

        // Aulas recentes
        $recent = $sessions->sortByDesc('date')->take(10);

        // Turmas do professor
        $classes = ClassOffering::whereIn('id', $classIds)
            ->with(['course', 'unit'])
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
            'units' => \App\Models\Unit::query()
                ->whereIn(
                    'id',
                    $classes->pluck('unit_id')->unique()
                )
                ->get(),
            'courses' => \App\Models\Course::all(),
            'classes' => $classes,
        ]);
    }
}
