<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\StudentDisciplineMonthRecord;
use App\Services\ClassLoadService;
use App\Services\ClassOfferingSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->string('search')->trim();
        $courseId = $request->integer('course_id');
        $projectId = $request->integer('project_id');
        $disciplineId = $request->integer('discipline_id');

        $baseQuery = ClassOffering::query()
            ->whereHas('disciplines', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            });

        $search = $request->input('search');

        $classes = $baseQuery
            ->when(! empty($search), function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('course', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('project', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('disciplines', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('students', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($courseId, fn ($query, $courseId) => $query->where('course_id', $courseId))
            ->when($projectId, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->when($disciplineId, fn ($query, $disciplineId) => $query->whereHas('disciplines', function ($query) use ($disciplineId, $user) {
                $query->where('id', $disciplineId)
                    ->where('teacher_id', $user->id);
            }))
            ->with([
                'course',
                'project',
                'unit',
                'disciplines' => fn ($query) => $query->where('teacher_id', $user->id),
            ])
            ->get();

        $availableClasses = ClassOffering::query()
            ->whereHas('disciplines', fn ($query) => $query->where('teacher_id', $user->id))
            ->with(['course', 'project', 'disciplines' => fn ($query) => $query->where('teacher_id', $user->id)])
            ->get();

        $courses = $availableClasses
            ->pluck('course')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $projects = $availableClasses
            ->pluck('project')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $disciplines = $availableClasses
            ->flatMap->disciplines
            ->unique('id')
            ->sortBy('name')
            ->values();

        $loadService = app(ClassLoadService::class);

        return view('teacher.classes.index', compact(
            'classes',
            'courses',
            'projects',
            'disciplines',
            'search',
            'courseId',
            'projectId',
            'disciplineId'
        ));
    }

    public function show(Request $request, ClassOffering $offering)
    {
        $user = Auth::user();

        if (! $user->isProfessorInOffering($offering)) {
            abort(403, 'Sem acesso a esta turma.');
        }

        $studentName = $request->string('student_name')->trim();
        $selectedDisciplineId = $request->integer('discipline_id');

        $disciplines = $offering->disciplines()
            ->where('teacher_id', $user->id)
            ->get();

        if (! $selectedDisciplineId || ! $disciplines->contains('id', $selectedDisciplineId)) {
            $selectedDisciplineId = $disciplines->first()?->id;
        }

        $selectedDiscipline = $disciplines->firstWhere('id', $selectedDisciplineId);

        $students = $offering->students()
            ->when(! empty($studentName), fn ($query) => $query->where('name', 'like', "%{$studentName}%"))
            ->get();

        $months = [];
        $period = \Carbon\CarbonPeriod::create(
            $offering->start_date,
            '1 month',
            $offering->end_date
        );

        foreach ($period as $date) {
            $months[] = $date->format('Y-m');
        }

        $monthRecords = [];
        $studentRecords = $offering->studentRecords->keyBy('student_id');
        $records = StudentDisciplineMonthRecord::where('class_offering_id', $offering->id)
            ->where('discipline_id', $selectedDisciplineId)
            ->get();

        foreach ($records as $r) {
            $key = $r->year.'-'.str_pad($r->month, 2, '0', STR_PAD_LEFT);
            $monthRecords[$r->student_id][$key] = $r;
        }

        $submissions = $offering->submissions
            ->keyBy(fn ($s) => $s->year.'-'.str_pad($s->month, 2, '0', STR_PAD_LEFT));

        $totalMonths = count($months);
        $done = $offering->submissions()->where('status', 'approved')->count();
        $progress = $totalMonths > 0 ? round(($done / $totalMonths) * 100) : 0;

        $disciplineProgress = [];
        foreach ($disciplines as $discipline) {
            $total = $discipline->pivot->workload ?? 0;
            $done = rand(0, $total);
            $disciplineProgress[$discipline->id] = $total > 0 ? round(($done / $total) * 100) : 0;
        }

        $loadService = app(ClassLoadService::class);

        $monthlyLoads = [];

        foreach ($months as $month) {

            [$year, $monthNumber] = explode('-', $month);

            $monthlyLoads[$month] = $selectedDiscipline
                ? $loadService->getMonthlyLoad(
                    $offering->id,
                    $selectedDiscipline->id,
                    (int) $monthNumber,
                    (int) $year
                )
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
            'disciplines',
            'selectedDiscipline',
            'studentName',
            'monthlyLoads',
        ));
    }

    public function storeMonthly(Request $request, ClassOffering $offering)
    {
        $request->validate([
            'records' => ['array'],
            'discipline_id' => ['required', 'exists:disciplines,id'],
        ]);

        $disciplineId = $request->input('discipline_id');

        foreach ($request->input('records', []) as $studentId => $months) {

            foreach ($months as $month => $row) {

                if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
                    continue;
                }

                [$year, $monthNumber] = explode('-', $month);

                $record = StudentDisciplineMonthRecord::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_offering_id' => $offering->id,
                        'discipline_id' => $disciplineId,
                        'month' => (int) $monthNumber,
                        'year' => (int) $year,
                    ],
                    [
                        'total_classes' => (int) ($row['total'] ?? 0),
                        'absences' => (int) ($row['absences'] ?? 0),
                        'justified_absences' => (int) ($row['justified'] ?? 0),
                    ]
                );

                $record->calculate();
                $record->save();
            }
        }

        return back()->with('success', 'Frequência por disciplina salva com sucesso.');
    }

    public function closeMonth(ClassOffering $offering, string $month)
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            abort(404);
        }

        [$year, $monthNumber] = explode('-', $month);

        app(ClassOfferingSubmissionService::class)
            ->createTeacherSubmission($offering, (int) $monthNumber, (int) $year);

        return back()->with('success', 'Frequência mensal enviada para homologação.');
    }
}
