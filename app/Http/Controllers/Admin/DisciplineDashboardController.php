<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Discipline;

class DisciplineDashboardController extends Controller
{
    public function index(ClassOffering $offering, Discipline $discipline)
    {
        // Obter pivot — disciplina específica na turma
        $pivot = $offering->disciplines()->where('discipline_id', $discipline->id)->first()->pivot;

        $plannedHours = $pivot->workload ?? $discipline->workload ?? 0;

        // Aulas da disciplina
        $sessions = $offering->sessions()
            ->where('discipline_id', $discipline->id)
            ->with('teacher')
            ->orderBy('date')
            ->get();

        $taughtHours = $sessions->sum('duration_hours');
        $remainingHours = max(0, $plannedHours - $taughtHours);
        $percent = $plannedHours > 0 ? round(($taughtHours / $plannedHours) * 100, 1) : 0;

        // KPIs
        $classesCount = $sessions->count();
        $teachersCount = $sessions->pluck('teacher_id')->unique()->count();
        
        // Hours by Month
        $hoursByMonth = $sessions->groupBy(fn($s) => $s->date->format('Y-m'))
            ->map(fn($g) => [
                'label' => $g->first()->date->format('m/Y'),
                'hours' => $g->sum('duration_hours'),
            ]);

        // Hours by weekday
        $hoursByWeekday = $sessions->groupBy(fn($s) => $s->date->format('N'))
            ->map(fn($g) => [
                'label' => $g->first()->date->translatedFormat('l'),
                'hours' => $g->sum('duration_hours'),
            ]);

        // Timeline of sessions
        $timeline = $sessions->map(fn($s) => [
            'date' => $s->date->format('d/m/Y'),
            'hours' => $s->duration_hours
        ]);

        return view('admin.class_offerings.discipline.dashboard', [
            'offering' => $offering,
            'discipline' => $discipline,
            'pivot' => $pivot,
            'sessions' => $sessions,
            'plannedHours' => $plannedHours,
            'taughtHours' => $taughtHours,
            'remainingHours' => $remainingHours,
            'percent' => $percent,
            'classesCount' => $classesCount,
            'teachersCount' => $teachersCount,
            'hoursByMonth' => $hoursByMonth,
            'hoursByWeekday' => $hoursByWeekday,
            'timeline' => $timeline,
        ]);
    }
}
