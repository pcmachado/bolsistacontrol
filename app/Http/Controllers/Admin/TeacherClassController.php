<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentDisciplineMonthRecord;
use App\Services\ClassLoadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $holderId = $user->scholarshipHolder?->id;

        $search = $request->string('search')->trim();
        $courseId = $request->integer('course_id');
        $projectId = $request->integer('project_id');
        $disciplineId = $request->integer('discipline_id');

        $baseQuery = ClassOffering::query()
            ->whereHas('disciplines', function ($query) use ($holderId) {
                $query->where('teacher_id', $holderId);
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
            ->when($disciplineId, fn ($query, $disciplineId) => $query->whereHas('disciplines', function ($query) use ($disciplineId, $holderId) {
                $query->where('id', $disciplineId)
                    ->where('teacher_id', $holderId);
            }))
            ->with([
                'course',
                'project',
                'unit',
                'disciplines' => fn ($query) => $query->where('teacher_id', $holderId),
            ])
            ->get();

        $availableClasses = ClassOffering::query()
            ->whereHas('disciplines', fn ($query) => $query->where('teacher_id', $holderId))
            ->with(['course', 'project', 'disciplines' => fn ($query) => $query->where('teacher_id', $holderId)])
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

    public function show(
    Request $request,
    ClassOffering $offering
    ) {

        $user = Auth::user();

        $holderId = $user
            ->scholarshipHolder?->id;

        if (! $user->isProfessorInOffering($offering)) {

            abort(403, 'Sem acesso a esta turma.');
        }

        /*
        |--------------------------------------------------------------------------
        | Disciplina
        |--------------------------------------------------------------------------
        */

        $disciplines = $offering
            ->disciplines()
            ->where('teacher_id', $holderId)
            ->get();

        $selectedDisciplineId = $request->integer(
            'discipline_id'
        );

        if (
            ! $selectedDisciplineId
            || ! $disciplines->contains(
                'id',
                $selectedDisciplineId
            )
        ) {

            $selectedDisciplineId = $disciplines
                ->first()?->id;
        }

        $selectedDiscipline = $disciplines
            ->firstWhere(
                'id',
                $selectedDisciplineId
            );

        $selectedOfferingDiscipline = $selectedDiscipline?->pivot?->id
            ? ClassOfferingDiscipline::query()
                ->with(['classOffering', 'discipline'])
                ->find($selectedDiscipline->pivot->id)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Navegação mensal
        |--------------------------------------------------------------------------
        */

        $startMonth = \Carbon\Carbon::parse(
            $offering->start_date
        )->startOfMonth();

        $endMonth = $offering->end_date
            ? \Carbon\Carbon::parse(
                $offering->end_date
            )->startOfMonth()
            : now()->startOfMonth();

        $requestedMonth = $request->get(
            'month',
            $startMonth->format('Y-m')
        );

        $currentMonth = \Carbon\Carbon::createFromFormat(
            'Y-m',
            $requestedMonth
        )->startOfMonth();

        if ($currentMonth->lt($startMonth)) {

            $currentMonth = $startMonth;
        }

        if ($currentMonth->gt($endMonth)) {

            $currentMonth = $endMonth;
        }

        $prevMonth = $currentMonth
            ->copy()
            ->subMonth();

        $nextMonth = $currentMonth
            ->copy()
            ->addMonth();

        $canGoPrev = $prevMonth->gte(
            $startMonth
        );

        $canGoNext = $nextMonth->lte(
            $endMonth
        );

        $monthKey = $currentMonth->format('Y-m');

        /*
        |--------------------------------------------------------------------------
        | Alunos
        |--------------------------------------------------------------------------
        */

        $studentName = $request
            ->string('student_name')
            ->trim();

        $students = $offering
            ->students()
            ->when(
                ! empty($studentName),
                fn ($query) =>
                    $query->where(
                        'name',
                        'like',
                        "%{$studentName}%"
                    )
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Registros mensais
        |--------------------------------------------------------------------------
        */

        [$year, $monthNumber] = explode(
            '-',
            $monthKey
        );

        $records = StudentDisciplineMonthRecord::query()

            ->where(
                'class_offering_id',
                $offering->id
            )

            ->where(
                'discipline_id',
                $selectedDisciplineId
            )

            ->where(
                'month',
                (int) $monthNumber
            )

            ->where(
                'year',
                (int) $year
            )

            ->get()

            ->keyBy('student_id');

        /*
        |--------------------------------------------------------------------------
        | Situação do mês
        |--------------------------------------------------------------------------
        */

        $submission = $offering
            ->submissions()

            ->where(
                'month',
                (int) $monthNumber
            )

            ->where(
                'year',
                (int) $year
            )

            ->first();

        $loadService = app(
            ClassLoadService::class
        );

        $monthlyPlanning = $selectedOfferingDiscipline
            ? $loadService->getMonthlyPlanning(
                $offering,
                $selectedOfferingDiscipline,
                (int) $monthNumber,
                (int) $year
            )
            : [
                'hours_per_day' => 0,
                'planned_total_hours' => 0,
                'planned_class_days' => 0,
                'monthly_planned_hours' => 0,
                'monthly_class_days' => 0,
            ];

        $monthlyLoad = $monthlyPlanning['monthly_planned_hours'];
        $monthlyClassDays = $monthlyPlanning['monthly_class_days'];
        $hoursPerDay = $monthlyPlanning['hours_per_day'];
        $plannedTotalHours = $monthlyPlanning['planned_total_hours'];
        $plannedClassDays = $monthlyPlanning['planned_class_days'];

        return view(
            'teacher.classes.show',
            compact(
                'offering',
                'disciplines',
                'selectedDiscipline',
                'students',
                'records',
                'submission',
                'monthlyLoad',
                'monthlyClassDays',
                'hoursPerDay',
                'plannedTotalHours',
                'plannedClassDays',
                'monthKey',
                'currentMonth',
                'prevMonth',
                'nextMonth',
                'canGoPrev',
                'canGoNext',
                'studentName'
            )
        );
    }

    public function storeMonthly(Request $request, ClassOffering $offering)
    {
        $request->validate([
            'records' => ['array'],
            'discipline_id' => ['required', 'exists:disciplines,id'],
        ]);

        $disciplineId = $request->input('discipline_id');
        $selectedOfferingDiscipline = ClassOfferingDiscipline::query()
            ->where('class_offering_id', $offering->id)
            ->where('discipline_id', $disciplineId)
            ->firstOrFail();

        foreach ($request->input('records', []) as $studentId => $months) {

            foreach ($months as $month => $row) {

                if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
                    continue;
                }

                [$year, $monthNumber] = explode('-', $month);
                $monthlyPlanning = app(
                    ClassLoadService::class
                )->getMonthlyPlanning(
                    $offering,
                    $selectedOfferingDiscipline,
                    (int) $monthNumber,
                    (int) $year
                );

                $submissionStatus = $offering->submissions()
                    ->where('month', (int) $monthNumber)
                    ->where('year', (int) $year)
                    ->value('status');

                if (in_array($submissionStatus, ['submitted', 'approved'], true)) {
                    continue;
                }

                $record = StudentDisciplineMonthRecord::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_offering_id' => $offering->id,
                        'discipline_id' => $disciplineId,
                        'month' => (int) $monthNumber,
                        'year' => (int) $year,
                    ],
                    [
                        'class_offering_discipline_id' => $selectedOfferingDiscipline->id,
                        'total_classes' => (int) (
                            $row['total']
                            ?? $monthlyPlanning['monthly_class_days']
                            ?? 0
                        ),
                        'classes_in_month' => (int) (
                            $monthlyPlanning['monthly_class_days']
                            ?? 0
                        ),
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

    public function closeMonth(Request $request, ClassOffering $offering) {

        $user = Auth::user();

        abort_unless(
            $user->isProfessorInOffering($offering),
            403
        );

        $validated = $request->validate([

            'discipline_id' => [
                'required',
                'exists:disciplines,id',
            ],

            'month' => [
                'required',
                'date_format:Y-m',
            ],

        ]);

        [$year, $month] = explode(
            '-',
            $validated['month']
        );

        /*
        |--------------------------------------------------------------------------
        | Verifica se já existe submissão
        |--------------------------------------------------------------------------
        */

        $submission = ClassOfferingSubmission::query()

            ->firstOrCreate(

                [
                    'class_offering_id' => $offering->id,

                    'discipline_id' =>
                        $validated['discipline_id'],

                    'month' => (int) $month,

                    'year' => (int) $year,
                ],

                [
                    'submitted_by' => $user->id,

                    'status' => 'submitted',

                    'submitted_at' => now(),
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Se já existia aberta
        |--------------------------------------------------------------------------
        */

        if ($submission->status === 'draft') {

            $submission->update([

                'status' => 'submitted',

                'submitted_by' => $user->id,

                'submitted_at' => now(),
            ]);
        }

        return redirect()

            ->route(
                'teacher.classes.show',
                [
                    $offering,
                    'discipline_id' =>
                        $validated['discipline_id'],

                    'month' =>
                        $validated['month'],
                ]
            )

            ->with(
                'success',
                'Mês fechado e enviado com sucesso.'
            );
    }
}
