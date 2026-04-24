<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TeacherClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $classes = ClassOffering::whereHas('disciplines', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })
            ->with([
                'course',
                'unit',
                'disciplines' => fn ($q) =>
                    $q->where('teacher_id', $user->id)
            ])
            ->get();

        return view('teacher.classes.index', compact('classes'));
    }

    public function show(ClassOffering $offering)
    {
        $user = Auth::user();

        // 🔒 segurança (IMPORTANTE)
        if (!$user->isProfessorInOffering($offering)) {
            abort(403, 'Sem acesso a esta turma.');
        }

        $students = $offering->students()->get();

        // meses
        $months = [];
        $period = \Carbon\CarbonPeriod::create(
            $offering->start_date,
            '1 month',
            $offering->end_date
        );

        foreach ($period as $date) {
            $months[] = $date->format('Y-m');
        }

        // registros
        $monthRecords = [];

        $studentRecords = $offering->studentRecords->keyBy('student_id');

        $records = \App\Models\StudentMonthRecord::where('class_offering_id', $offering->id)->get();

        foreach ($records as $r) {
            $key = $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT);
            $monthRecords[$r->student_id][$key] = $r;
        }

        // submissions
        $submissions = $offering->submissions
            ->keyBy(fn($s) => $s->year . '-' . str_pad($s->month, 2, '0', STR_PAD_LEFT));

        // progresso da turma
        $totalMonths = count($months);
        $done = $offering->submissions()->where('status', 'approved')->count();

        $progress = $totalMonths > 0 ? round(($done / $totalMonths) * 100) : 0;

        // 🔥 progresso por disciplina (APENAS DO PROFESSOR)
        $disciplineProgress = [];

        $disciplines = $offering->disciplines()
            ->where('teacher_id', $user->id)
            ->get();

        foreach ($disciplines as $discipline) {

            $total = $discipline->pivot->workload ?? 0;

            // TODO: substituir por cálculo real depois
            $done = rand(0, $total);

            $disciplineProgress[$discipline->id] = $total > 0
                ? round(($done / $total) * 100)
                : 0;
        }

        return view('teacher.classes.show', compact(
            'offering',
            'students',
            'months',
            'monthRecords',
            'submissions',
            'progress',
            'studentRecords',
            'disciplineProgress',
            'disciplines'
        ));
    }
}