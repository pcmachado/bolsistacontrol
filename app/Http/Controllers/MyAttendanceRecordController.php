<?php

namespace App\Http\Controllers;

use App\DataTables\MyAttendanceRecordDataTable;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Services\AttendanceService;
use App\Services\AttendanceSubmissionService;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAttendanceRecordController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $submissions,
        protected ScholarshipHolderService $scholarshipHolderService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request, MyAttendanceRecordDataTable $dataTable)
    {
        $user = Auth::user();
        $context = $this->scholarshipHolderService->attendanceContext(
            $user,
            $request->integer('project_id') ?: null
        );

        $holder = $context['holder'];
        $activeProjectId = $context['activeProjectId'];

        $monthString = $request->get('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $monthString);

        $year = (int) $year;
        $monthNumber = (int) $monthNumber;

        $attendanceService = app(AttendanceService::class);
        $total = $attendanceService->getMonthlyTotal($holder, $year, $monthNumber, $activeProjectId);
        $limit = $attendanceService->getMonthlyLimit($holder, $activeProjectId);

        $oldestRecord = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($query) => $query->where('project_id', $activeProjectId))
            ->orderBy('date')
            ->first();

        $currentMonth = now()->format('Y-m');
        $oldestMonth = $oldestRecord
            ? $oldestRecord->date->format('Y-m')
            : $currentMonth;

        $filters = [
            'month' => $monthString,
            'status' => $request->get('status'),
            'project_id' => $activeProjectId,
        ];

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($query) => $query->where('project_id', $activeProjectId))
            ->where('year', $year)
            ->where('month', $monthNumber)
            ->latest('id')
            ->first();

        return $dataTable
            ->setFilters($filters)
            ->render('attendance.my', [
                ...$context,
                'month' => $monthString,
                'year' => $year,
                'monthNumber' => $monthNumber,
                'total' => $total,
                'limit' => $limit,
                'currentMonth' => $currentMonth,
                'oldestMonth' => $oldestMonth,
                'oldestRecord' => $oldestRecord,
                'submission' => $submission,
                'isClosed' => ! $this->submissions->canCreateRecord($holder, $year, $monthNumber, $activeProjectId),
            ]);
    }
}
