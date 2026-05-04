<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;

class ClassOfferingDashboardController extends Controller
{
    public function index(ClassOffering $offering)
    {
        $sessions = ClassSession::where('class_offering_id', $offering->id)
            ->with(['discipline', 'teacher'])
            ->orderBy('date')
            ->get();

        // KPI 1 — Total horas ministradas
        $totalHours = $sessions->sum('duration_hours');

        // KPI 2 — Total de aulas registradas
        $totalClasses = $sessions->count();

        // KPI 3 — Professores envolvidos
        $teachers = $sessions->pluck('teacher')->unique('id')->count();

        // KPI 4 — Disciplinas com aulas
        $discWithAulas = $sessions->pluck('discipline')->unique('id')->count();

        // Gráfico — Horas por disciplina
        $hoursByDiscipline = $sessions->groupBy('discipline_id')
            ->map(fn ($g) => [
                'label' => $g->first()->discipline->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        // Gráfico — Horas por professor
        $hoursByTeacher = $sessions->groupBy('teacher_id')
            ->map(fn ($g) => [
                'label' => $g->first()->teacher->name,
                'hours' => $g->sum('duration_hours'),
            ]);

        // Gráfico — Horas por mês
        $hoursByMonth = $sessions->groupBy(fn ($s) => $s->date->format('Y-m'))
            ->map(fn ($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        return view('admin.class-offerings.dashboard.index', [
            'offering' => $offering,
            'totalHours' => $totalHours,
            'totalClasses' => $totalClasses,
            'teachersCount' => $teachers,
            'disciplinesCount' => $discWithAulas,
            'hoursByDiscipline' => $hoursByDiscipline,
            'hoursByTeacher' => $hoursByTeacher,
            'hoursByMonth' => $hoursByMonth,
        ]);
    }
}
