<?php

namespace App\Http\Controllers;

use App\DataTables\MyAttendanceRecordDataTable;
use App\Models\AttendanceRecord;
use App\Services\AttendanceRecordService;
use App\Services\AttendanceService;
use App\Services\AttendanceSubmissionService;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAttendanceRecordController extends Controller
{
    public function __construct(
        protected AttendanceRecordService $records,
        protected AttendanceSubmissionService $submissions,
        protected ScholarshipHolderService $scholarshipHolderService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request, MyAttendanceRecordDataTable $dataTable)
    {
        $user = Auth::user();
        $holder = $this->scholarshipHolderService->holderOrFail($user);

        $monthString = $request->get('month', now()->format('Y-m'));

        [$year, $monthNumber] = explode('-', $monthString);

        $year = (int) $year;
        $monthNumber = (int) $monthNumber;

        $attendanceService = app(AttendanceService::class);

        $total = $attendanceService->getMonthlyTotal($holder, $year, $monthNumber);
        $limit = $attendanceService->getMonthlyLimit($holder);

        $oldestRecord = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->orderBy('date')
            ->first();

        $currentMonth = now()->format('Y-m');
        $oldestMonth = $oldestRecord
            ? $oldestRecord->date->format('Y-m')
            : $currentMonth;

        $filters = [
            'month'  => $monthString,
            'status' => $request->get('status'),
        ];

        return $dataTable
            ->setFilters($filters)
            ->render('attendance.my', [
                'month'         => $monthString,
                'year'          => $year,
                'monthNumber'   => $monthNumber,
                'total'         => $total,
                'limit'         => $limit,
                'currentMonth'  => $currentMonth,
                'oldestMonth'   => $oldestMonth,
                'oldestRecord'  => $oldestRecord,
                'submission'    => null,
            ]);
    }
}
