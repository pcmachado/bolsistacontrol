<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Student;
use App\Models\StudentRecord;
use App\Models\ClassOfferingSubmission;
use App\Models\AttendanceSubmission;
use App\DataTables\ClassOfferingStudentDataTable;
use App\Services\ClassOfferingSubmissionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassOfferingStudentController extends Controller
{
    public function list(ClassOffering $class, ClassOfferingStudentDataTable $dataTable)
    {
        return $dataTable
            ->forClass($class->id)
            ->render('admin.class-offerings.students.list', [
                'class' => $class
            ]);
    }

    public function index(ClassOffering $class, Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $current = Carbon::create($year, $month, 1);

        $prev = $current->copy()->subMonth();
        $next = $current->copy()->addMonth();

        $start = Carbon::parse($class->start_date)->startOfMonth();
        $end   = Carbon::parse($class->end_date)->endOfMonth();

        $canGoPrev = $prev->gte($start);
        $canGoNext = $next->lte($end);

        if ($current->lt($start) || $current->gt($end)) {
            return redirect()
                ->route('admin.class.students.index', [
                    'class' => $class->id,
                    'month' => $start->month,
                    'year'  => $start->year
                ])
                ->with('warning', 'Período fora do intervalo da turma.');
        }

        $availableMonths = StudentRecord::where('class_offering_id', $class->id)
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $students = $class->students()
            ->with('classOfferings')
            ->get();

        $records = StudentRecord::where('class_offering_id', $class->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->keyBy('student_id');

        $rate = $class->project->student_daily_rate ?? 0;

        $submission = $class->submissions()
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $monthsData = collect();

        $cursor = $start->copy();

        while ($cursor <= $end) {

            $submission = $class->submissions()
                ->where('month', $cursor->month)
                ->where('year', $cursor->year)
                ->first();

            $status = $submission?->status ?? AttendanceSubmission::STATUS_DRAFT;

            $canSubmitCurrentMonth = app(ClassOfferingSubmissionService::class)
                ->canSubmitMonth($class, $cursor->month, $cursor->year);

            $monthsData->push([
                'month' => $cursor->month,
                'year'  => $cursor->year,
                'status' => $status,
                'canSubmit' => $canSubmitCurrentMonth,
            ]);

            $cursor->addMonth();
        }

        return view('admin.class-offerings.students.index', compact(
            'class',
            'students',
            'records',
            'rate',
            'submission',
            'month',
            'year',
            'prev',
            'next',
            'availableMonths',
            'canGoPrev',
            'canGoNext',
            'monthsData',
            'canSubmitCurrentMonth'
        ));
    }

    public function save(Request $request, ClassOffering $class)
    {
        $data = $request->input('students', []);

        foreach ($data as $studentId => $row) {

            $total = (int) ($row['total_classes'] ?? 0);
            $absences = (int) ($row['absences'] ?? 0);

            $rate = $class->project->student_daily_rate ?? 0;

            $record = StudentRecord::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_offering_id' => $class->id,
                ],
                [
                    'total_classes' => $total,
                    'absences' => $absences,
                    'daily_rate' => $rate,
                    'status' => $row['status'] ?? 'approved',
                ]
            );

            // 🔥 AQUI entra sua regra centralizada
            $record->calculate();
            $record->save();
        }

        return back()->with('success', 'Lançamentos salvos com sucesso.');
    }

    public function submit(Request $request, ClassOffering $class)
    {
        $month = $request->get('month');
        $year  = $request->get('year');

        $service = app(ClassOfferingSubmissionService::class);

        if (!$service->canSubmitMonth($class, $month, $year)) {
            return back()->with('error', 'Envie o mês anterior primeiro.');
        }

        $service->submit($class, $month, $year);

        return back()->with('success', 'Mês enviado com sucesso.');
    }
}